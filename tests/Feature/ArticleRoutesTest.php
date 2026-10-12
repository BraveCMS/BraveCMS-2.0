<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\ArticleCategory;
use Tests\TestCase;

class ArticleRoutesTest extends TestCase
{
    public function testGetBasePathRendersArticleView(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertViewIs('themes.calvin.templates.index');
    }

    public function testGetCategoryById(): void
    {
        $firstCategory = ArticleCategory::first();
        $this->assertNotNull($firstCategory);
        $articles = Article::where('category_id', $firstCategory->id)
            ->orderBy('id', 'desc')
            ->with(['category', 'user'])
            ->paginate(12);
        $this->get('/category/' . $firstCategory->id)
            ->assertOk()
            ->assertViewIs('themes.calvin.templates.index')
            ->assertViewHas('category', $firstCategory)
            ->assertViewHas('articles', function ($viewArticles) use ($articles) {
                return $viewArticles->modelKeys() === $articles->modelKeys()
                && $viewArticles->total() === $articles->total();
            });
    }
}
