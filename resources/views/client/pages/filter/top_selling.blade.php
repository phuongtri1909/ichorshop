@extends('client.pages.filter.filter-layout')

@section('title', 'Top Sales - Shop Now')
@section('description', 'Explore our top sale items and get the best deals. Shop now for exclusive discounts on the latest fashion!')
@section('keywords', 'top sales, discounts, deals, exclusive offers, sale items')

@php
$title = 'Top Sales';
$breadcrumbItems = [
    ['title' => 'Home', 'url' => route('home')],
    ['title' => 'Top Sales', 'url' => null, 'active' => true]
];
@endphp