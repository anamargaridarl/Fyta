@extends('layouts.app', ['scripts' => [], 'styles' => ['css/loginpage.css']])


@section('content')
<div class="container">
  <form method="POST" action="/login">
    @csrf
    <div class="form ">
      <div class="row ">
        <div class="col">
          <h1 class="text-center form-title">Login</h1>
        </div>
      </div>
      <div class="form-group row">
        <div class="col">
          <label for="email">Email</label>
          <input type="text" name="email" id="email" class="form-control" placeholder="" aria-describedby="helpId">
        </div>
      </div>
      <div class="form-group row">
        <div class="col">
          <label for="password">Password</label>
          <input type="password" name="password" id="password" class="form-control" placeholder="" aria-describedby="helpId">
        </div>
      </div>
      <div class="row ">
        <div class="col button">
          <label for="start">
          <input type="submit" class="btn rounded-0 btn-lg shadow-none" id="start" value="Start Session">
          </label>
        </div>
      </div>
      <div class="row register-info justify-content-center">
          <p>Don't have an account yet?  </p>
          <small id="helpId" class=" text-muted">
            <a href="/register">Register now</a>
          </small>
      </div>
    </div>
  </form>
</div>
@include('components.errors')
@endsection