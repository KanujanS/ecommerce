<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container">

    <!-- Brand - Now navigates to Home Page -->
    <a class="navbar-brand fw-bold" href="{{ url('/') }}">NSD</a>

    <!-- Toggle Button for Mobile -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Nav Items Centered -->
    <div class="collapse navbar-collapse justify-content-center" id="navbarText">
      <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Products</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">About Us</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Contact Us</a>
        </li>
      </ul>
    </div>

    <!-- Login / Logout Button on the Right -->
    <div class="d-flex">
        @auth
            <a href="/logout">
                <button class="btn btn-outline-danger">Logout</button>
            </a>
        @else
            <a href="/login">
                <button class="btn btn-outline-success">Login</button>
            </a>
        @endauth
    </div>

  </div>
</nav>