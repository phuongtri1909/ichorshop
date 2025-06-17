@extends('client.pages.filter.filter-layout')

@section('title', 'New Arrivals - Shop Now')
@section('description', 'Discover the latest fashion trends with our new arrivals. Shop now for the freshest styles and exclusive collections!')
@section('keywords', 'new arrivals, latest fashion, trendy clothes, exclusive collections')

@php
$title = 'New Arrivals';
$breadcrumbItems = [
    ['title' => 'Home', 'url' => route('home')],
    ['title' => 'New Arrivals', 'url' => null, 'active' => true]
];
@endphp