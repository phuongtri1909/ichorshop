@extends('client.pages.filter.filter-layout')

@section('title', 'Category - ' . $category->name)
@section('description', 'Explore our collection of ' . $category->name . '. Find the perfect items that fit your style. Shop now!')
@section('keywords', $category->name . ', collection, style')

@php
$title = 'Category - ' . $category->name;
$showCategoryFilter = false;
@endphp