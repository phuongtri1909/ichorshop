@extends('client.pages.filter.filter-layout')

@section('title', 'Search Results')
@section('description', 'Discover the best deals and discounts on our search results page. Shop exclusive offers and find your favorite items at unbeatable prices.')
@section('keywords', 'search results, discounts, deals, exclusive offers, sale items')

@php
$title = 'Search Results';
$breadcrumbItems = [
    ['title' => 'Home', 'url' => route('home')],
    ['title' => 'Search Results', 'url' => null, 'active' => true]
];
@endphp