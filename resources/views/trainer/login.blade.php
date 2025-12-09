@extends('layouts.auth')
@section('title', 'Login')

@section('content')
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">

                <div class="row justify-content-center">
                    <div class="col-md-4 col-lg-3"> <!-- Reduced width -->

                        <div class="card">
                            <div class="card-body">

                                <h4 class="mb-2 text-center">Welcome Back 👋</h4>
                                <p class="mb-4 text-center">Sign in to continue</p>

                                <form id="loginForm">

                                    <!-- Login Field -->
                                    <div class="mb-3">
                                        <label class="form-label">Email or Phone</label>
                                        <input type="text" class="form-control" id="login" name="login"
                                            placeholder="Enter email or phone" />
                                        <small class="text-danger d-block" id="login_error"></small>
                                    </div>

                                    <!-- Password Field -->
                                    <div class="mb-3">
                                        <label class="form-label">Password</label>
                                        <input type="password" class="form-control" id="password" name="password"
                                            placeholder="********" />
                                        <small class="text-danger d-block" id="password_error"></small>
                                    </div>

                                    <!-- Submit -->
                                    <div class="mb-3">
                                        <button class="btn btn-primary w-100" type="submit" id="loginBtn">
                                            Sign In
                                        </button>
                                    </div>

                                    <div id="general_error" class="text-danger text-center"></div>
                                </form>

                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection
