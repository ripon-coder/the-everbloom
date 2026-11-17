@extends('layouts.app')

@section('title', $page->meta_title ?: ($page->title . ' — ' . config('demo.site.name', 'The Everbloom')))
@if($page->meta_description)
    @section('meta_description', $page->meta_description)
@endif

@section('content')
<div class="site-container py-12 md:py-16 space-y-10">
    <x-storefront.breadcrumbs :items="[['label' => $page->title, 'url' => '']]" />

    <div class="max-w-3xl mx-auto">
        <h1 class="font-serif text-3xl sm:text-4xl text-ink font-normal mb-6">{{ $page->title }}</h1>
        <div class="prose prose-stone max-w-none text-sm sm:text-base text-ink/90 leading-relaxed space-y-4">
            {!! $page->sanitizedContent() !!}
        </div>
    </div>
</div>
@endsection

