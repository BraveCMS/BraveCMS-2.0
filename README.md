# Brave CMS 2.0

## What is this about? 

This is a simple, easy to use, CMS, ideal for an online newspaper or a blog. Laravel masters are invited to *contribute*.

#### Why use it

1. It's written in Laravel 8.
2. It's scalable.
3. The code is DRY and easy-to-follow, so contributors will have no headaches.
4. The newsletter form is integrated with **Mailchimp**.
5. Its Admin is designed with **Bootstrap 5 SASS** and very easy to customize.
6. It's free and open source.

## Support Brave CMS

Brave CMS is free, open source, and maintained in my spare time.

If you find the project useful, please consider supporting its continued development. Your sponsorship helps fund new features, bug fixes, documentation, security updates, and long-term maintenance.

⭐ Star this repository if you like the project.

❤️ Sponsor Brave CMS on Open Collective:
https://opencollective.com/brave-cms

### Video demo

[![Watch the demo](https://github.com/Ajax30/BraveCMS-2.0/blob/main/screenshots/video-thumbnail.png)](https://vimeo.com/manage/videos/1169547458)

### Login form

![Login](https://github.com/Ajax30/BraveCMS-2.0/blob/main/screenshots/login.png)

### Dashboard

![Dashboard](https://github.com/Ajax30/BraveCMS-2.0/blob/main/screenshots/dashboard.png)

### Articles

![Articles list](https://github.com/Ajax30/BraveCMS-2.0/blob/main/screenshots/articles-list.png)

### Add article

![Add article](https://github.com/Ajax30/BraveCMS-2.0/blob/main/screenshots/article-add.png)

### Edit article

![Edit article](https://github.com/Ajax30/BraveCMS-2.0/blob/main/screenshots/article-edit.png)

### Site settings

The **super-admin** has access to the *Settings* section.

![Settings preview](https://github.com/Ajax30/BraveCMS-2.0/blob/main/screenshots/settings.png)


# How to use Brave CMS

I have created a simple installation process: after creating a database and providing credentials (the appropriate values for `DB_DATABASE`, `DB_USERNAME` and `DB_PASSWORD`) in the `.env` file (placed in the root directory), you can run the `Install` controller which will create all the necessary tables.

Alternatively, you can import `minimal.sql`, which contains all the necessary tables. You can find it in the root of the repository.

After that, you will be invited to *sign up*. After a successful sign-up, because you are the *first user*, you will be given the role of `super-admin`.

You will be able to sign in and write articles, add or delete categories, etc.


# Brave CMS Theme Development Guide

## Variables → Template Reference

| Variable            | Description                                              |
|--------------------|----------------------------------------------------------|
| `$article`         | Current article object (title, content, meta, etc.)      |
| `$article_count`   | Total number of articles in the current listing          |
| `$articles`        | Collection of articles (used for loops/lists)            |
| `$author`          | Author data for current context (name, bio, etc.)        |
| `$categories`      | List of all site categories                              |
| `$category`        | Current category being viewed                            |
| `$comments`        | List of comments for the current article                 |
| `$is_cookieconsent`| Boolean flag to toggle cookie consent display            |
| `$page`            | Current static page object                               |
| `$pages`           | List of all static pages                                 |
| `$search_query`    | Current search query string                              |
| `$site_name`       | Website name                                             |
| `$subtitle`        | Subtitle of the 404 template                      |
| `$tag`             | Current tag being viewed                                 |
| `$tagline`         | Website tagline/description                              |
| `$title`           | Title of the 404 template                           |
| `$theme_directory` | Path/URL to the active theme directory                   |

---

## Creating a Theme

Brave CMS supports custom themes located in:

```bash
resources/views/themes/
```

---

## Theme Directory Variable

All templates reference the active theme dynamically using `$theme_directory`.

Example:

```blade
@extends('themes/' . $theme_directory . '/layout')
@include('themes/' . $theme_directory . '/partials/header')
```

---

## Theme Structure

A typical theme structure:

```text
resources/views/themes/mytheme/

layout.blade.php

partials/
    header.blade.php
    footer.blade.php

templates/
    index.blade.php
    single.blade.php
    page.blade.php
    contact.blade.php
    404.blade.php
```

Public assets:

```text
public/themes/mytheme/
├── css/
│   └── style.css
├── js/
│   └── script.js
└── images/
```

Include CSS/JS in the layout:

```blade
<link rel="stylesheet" href="{{ asset('themes/' . $theme_directory . '/css/style.css') }}">
<script src="{{ asset('themes/' . $theme_directory . '/js/script.js') }}"></script>
```

---

## Layout

`layout.blade.php` defines the main page structure:

```blade
@include('themes/' . $theme_directory . '/partials/header')

@yield('content')

@include('themes/' . $theme_directory . '/partials/footer')
```

Templates extend the layout:

```blade
@extends('themes/' . $theme_directory . '/layout')
```

---

## Templates

### `index.blade.php` — Article List

Example:

```blade
@extends('themes/' . $theme_directory . '/layout')

@section('content')
@if (count($articles))
  <div class="row articles-grid">
    @foreach ($articles as $article)
      <h2>{{ $article->title }}</h2>
      <p>{{ $article->short_description }}</p>
      <a href="{{ url('/show/' . $article->slug) }}">Read more</a>
    @endforeach
  </div>
@endif

{!! $articles->onEachSide(1)->withQueryString()->links() !!}
@endsection
```

### `single.blade.php` — Single Article

Example:

```blade
@extends('themes/' . $theme_directory . '/layout')

@section('content')
  <article>
    <h1>{{ $article->title }}</h1>
    <p>{{ $article->short_description }}</p>
    <div class="article-content">
     {!! $article->content !!}
    </div>
  </article>
@endsection
```

### `page.blade.php` — Static Page

Example:

```blade
@extends('themes/' . $theme_directory . '/layout')

@section('content')
  <h1>{{ $page->title }}</h1>
    <div class="page-content">
     { !! $page->content !!}
    </div>
@endsection
```

### `contact.blade.php` — Contact Page

Example:

```blade
@extends('themes/' . $theme_directory . '/layout')

@section('content')
  <div class="container">
    <h3>Contact us</h3>
      <form method="post" action="{{ route('contact.submit') }}">
      @csrf
        <input type="text" name="name" placeholder="Your Name">
        <input type="email" name="email" placeholder="Your Email">
        <textarea name="message" placeholder="Your Message"></textarea>
        <input type="submit" value="Send">
      </form>
  </div>
@endsection
```

### `404.blade.php` — 404 Page

Example:

```blade
@extends('themes/' . $theme_directory . '/layout')

@section('content')
  <div class="container text-center">
    <h1>{{ $title }}</h1>
    <p class="text-muted">{{ $subtitle }}</p>
  </div>
@endsection
```

---

## Partials

Always use `$theme_directory` when including partials.

Example:

```
@include('themes/' . $theme_directory . '/partials/header')
@include('themes/' . $theme_directory . '/partials/footer')
```

### `header.blade.php` — header partial
Example:
```
<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ $tagline }} | {{ $site_name }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ $article->short_description ?? '' }}">
    <link rel="icon" href="{{ asset('themes/' . $theme_directory . '/img/favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('themes/' . $theme_directory . '/css/style.css') }}">
</head>
<body>
```

# License

MIT License

Copyright (c) 2022 Razvan Zamfir

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
