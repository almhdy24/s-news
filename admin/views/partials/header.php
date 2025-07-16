<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4 fixed-top shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="/">
      <i class="bi bi-speedometer2 me-1"></i> S-News Admin
    </a>

    <button
      class="navbar-toggler"
      type="button"
      data-bs-toggle="collapse"
      data-bs-target="#adminNavbar"
      aria-controls="adminNavbar"
      aria-expanded="false"
      aria-label="Toggle navigation"
    >
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="adminNavbar">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link" href="/admin">
            <i class="bi bi-house-door me-1"></i> Dashboard
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="/admin/news">
            <i class="bi bi-newspaper me-1"></i> News
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="/admin/categories">
            <i class="bi bi-tags me-1"></i> Categories
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="/admin/visitors">
            <i class="bi bi-bar-chart-line me-1"></i> Visitors Logs
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="/admin/settings">
            <i class="bi bi-gear-fill me-1"></i> Settings
          </a>
        </li>
      </ul>

      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <span class="nav-link">
            <i class="bi bi-person-circle me-1"></i> almhdy
          </span>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="/admin/logout">
            <i class="bi bi-box-arrow-right me-1"></i> Logout
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>