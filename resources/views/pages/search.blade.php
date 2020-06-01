@extends('layouts.app', ['scripts' => ['js/searchpage.js'], 'styles' => ['css/searchpage.css', 'css/pallette.css']])

@section('content')
  <form class="input-group w-auto mt-1 rounded-pill border border-dark navbar-search-mobile" action="/search" method="GET">
    <span class="input-group-append">
        <button type="submit" class="btn border border-right-0">
            <i aria-hidden="true" class="fas fa-search form-control-feedback"></i>
        </button>
    </span>
    <label for="query">Search for a product...</label>
    <input class="form-control border-left-0" type="text" id="query" name="query" placeholder="Search for a product...">
  </form>
  <div class="title">
    <div class="row">
      <div class="col">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/search">Search</a></li>
          </ol>
        </nav>
      </div>
      <div class="col">
        <div class="dropdown">
          <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Order By
          </button>
          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <a id="ordByPop" class="dropdown-item" >Match</a>
            <a id="ordByPrice" class="dropdown-item" >Price</a>
          </div>
        </div>
        <div class="searchbuttons">
          <div id="order">
            <button type="button" class="btn btn-outline-dark" data-toggle="modal" data-target="#exampleModalCenter">Order by
              <i class="fas fa-chevron-down"></i>
            </button>
            <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenter" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-body">
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="exampleRadios" id="matchOrdBy" value="option1" checked>
                      <label class="form-check-label" for="matchOrdBy">
                        Match
                      </label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="exampleRadios" id="priceOrdBy" value="option1">
                      <label class="form-check-label" for="priceOrdBy">
                        Price
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div id="filter">
            <button type="button" class="btn btn-outline-dark">Filters
              <i class="fas fa-chevron-down"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="content">
    <div class="row justify-content-between" id="parent">
      <div class="col-lg-3 ">
        <h5>Categories</h5>
        <ul id="categories" class="list-group list-group-flush">
          @each('components.search_categories', $categories, 'category')
        </ul>
        <h5>Size</h5>
        <ul id="sizes" class="list-group list-group-flush">
            @each('components.search_sizes', $sizes, 'size')
        </ul>
        <div class="price">
          <h5>Price Range</h5>
          <div class="row price-values justify-content-around">
            <div class="col-5 min">
              <p>1€</p>
            </div>
            <div class="col-5 max">
              <p>100€</p>
            </div>
          </div>
          <div class="row price-inputs justify-content-around">
            <div class="col-5 min-input">
              <label for="min">Minimum Price:</label>
              <input type="number" class="form-control" id="min" placeholder="1" min="1" max="99">
            </div>
            <div class="col-5 max-input">
              <label for="max">Maximum Price:</label>
              <input type="number" class="form-control" id="max" placeholder="100" min="2" max="100">
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-8">
        <div class="row row-cols-lg-3 row-cols-md-3 row-cols-sm-2 row-cols-2"></div>
      </div>
    </div>
@endsection
