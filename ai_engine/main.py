"""
Semantic 404 Recovery – AI Engine
FastAPI application that performs mock semantic search to match broken URLs
with the closest known site content.
"""

from fastapi import FastAPI, Query
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
import math

app = FastAPI(
    title="Semantic 404 AI Engine",
    description="Returns the closest matching page for a broken URL path.",
    version="1.0.0",
)

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_methods=["GET"],
    allow_headers=["*"],
)

# ---------------------------------------------------------------------------
# Mock content catalogue – replace with a real vector store in production.
# Each entry: (url, title, snippet, keywords[])
# ---------------------------------------------------------------------------
SITE_CONTENT = [
    {
        "url": "/investment-strategies",
        "title": "Investment Strategies",
        "snippet": "Discover high-yield investment options tailored for you.",
        "keywords": ["investment", "invest", "strategies", "portfolio", "yield", "returns", "finance"],
    },
    {
        "url": "/about",
        "title": "About Us",
        "snippet": "Learn about our mission, vision, and expert financial team.",
        "keywords": ["about", "team", "mission", "vision", "company", "us", "who"],
    },
    {
        "url": "/contact",
        "title": "Contact Us",
        "snippet": "Get in touch with our support team for personalised guidance.",
        "keywords": ["contact", "reach", "support", "help", "email", "phone", "touch"],
    },
    {
        "url": "/loans",
        "title": "Personal Loans",
        "snippet": "Flexible personal loan options with competitive interest rates.",
        "keywords": ["loan", "loans", "borrow", "credit", "personal", "debt", "interest"],
    },
    {
        "url": "/savings",
        "title": "Savings Accounts",
        "snippet": "Maximise your savings with our high-interest account plans.",
        "keywords": ["savings", "save", "account", "deposit", "money", "interest", "bank"],
    },
    {
        "url": "/insurance",
        "title": "Insurance Plans",
        "snippet": "Protect what matters most with our comprehensive insurance coverage.",
        "keywords": ["insurance", "insure", "plan", "coverage", "protect", "risk", "policy"],
    },
    {
        "url": "/crypto",
        "title": "Crypto Trading",
        "snippet": "Trade Bitcoin, Ethereum, and top altcoins on our secure platform.",
        "keywords": ["crypto", "bitcoin", "ethereum", "blockchain", "trade", "digital", "coin"],
    },
    {
        "url": "/retirement",
        "title": "Retirement Planning",
        "snippet": "Plan your retirement with expert advice and secure pension schemes.",
        "keywords": ["retirement", "retire", "pension", "plan", "future", "age", "savings"],
    },
    {
        "url": "/blog",
        "title": "Financial Blog",
        "snippet": "Expert insights, market news, and financial tips updated weekly.",
        "keywords": ["blog", "news", "article", "tips", "insights", "market", "read"],
    },
    {
        "url": "/privacy-policy",
        "title": "Privacy Policy",
        "snippet": "Understand how we collect, use, and protect your personal data.",
        "keywords": ["privacy", "policy", "data", "gdpr", "personal", "legal", "terms"],
    },
]


def _tokenise(text: str) -> list[str]:
    """Lowercase and split path segments / words."""
    import re
    return re.findall(r"[a-z]+", text.lower())


def _score(path_tokens: list[str], entry: dict) -> float:
    """
    Simple TF-based cosine-like similarity score in [0, 1].
    Weights keyword hits and URL segment matches.
    """
    all_keywords = set(entry["keywords"])
    url_tokens = set(_tokenise(entry["url"]))

    hits = 0
    for token in path_tokens:
        if token in all_keywords:
            hits += 1.4          # keyword hit – higher weight
        elif token in url_tokens:
            hits += 1.0          # URL segment hit
        else:
            # partial substring match
            for kw in all_keywords:
                if token in kw or kw in token:
                    hits += 0.5
                    break

    if not path_tokens:
        return 0.0

    # Normalise to [0, 1] using a sigmoid-like squash
    raw = hits / len(path_tokens)
    score = 1 - math.exp(-raw * 0.9)
    return round(min(score, 0.99), 4)


class MatchResult(BaseModel):
    title: str
    url: str
    snippet: str
    score: float


@app.get("/match", response_model=MatchResult)
def match_path(path: str = Query(..., description="The broken URL path to match")):
    """
    Accepts a broken URL path and returns the best-matching page from the
    site content catalogue using keyword-similarity scoring.
    """
    tokens = _tokenise(path)
    best = max(SITE_CONTENT, key=lambda e: _score(tokens, e))
    best_score = _score(tokens, best)

    return MatchResult(
        title=best["title"],
        url=best["url"],
        snippet=best["snippet"],
        score=best_score,
    )


@app.get("/health")
def health():
    return {"status": "ok", "service": "semantic-404-ai-engine"}


@app.get("/")
def root():
    return {
        "message": "Semantic 404 AI Engine is running.",
        "docs": "/docs",
        "match_endpoint": "/match?path=<broken-path>",
    }
