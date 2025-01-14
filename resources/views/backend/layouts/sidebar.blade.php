  <!-- ========== Left Sidebar Start ========== -->
  <div class="leftside-menu">

      <!-- LOGO -->
      <a href="index.html" class="logo text-center logo-light">
          <span class="logo-lg">
              <img src="{{asset('backend/assets/images/logo.png')}}" alt="" height="16">
          </span>
          <span class="logo-sm">
              <img src="{{asset('backend/assets/images/logo_sm.png')}}" alt="" height="16">
          </span>
      </a>

      <!-- LOGO -->
      <a href="index.html" class="logo text-center logo-dark">
          <span class="logo-lg">
              <img src="{{asset('backend/assets/images/logo-dark.png')}}" alt="" height="16">
          </span>
          <span class="logo-sm">
              <img src="{{asset('backend/assets/images/logo_sm_dark.png')}}" alt="" height="16">
          </span>
      </a>

      <div class="h-100" id="leftside-menu-container" data-simplebar="">

          <!--- Sidemenu -->
          <ul class="side-nav">

              <!-- <li class="side-nav-title side-nav-item">Navigation</li> -->

              <li class="side-nav-item">
                  <a href="#" data-bs-toggle="collapse" href="#sidebarDashboards" aria-expanded="false" aria-controls="sidebarDashboards" class="side-nav-link">
                      <i class="uil-home-alt"></i>
                      <!-- <span class="badge bg-success float-end">4</span> -->
                      <span>
                          {{__('Dashboard')}}
                      </span>
                  </a>
              </li>

              <li class="side-nav-item">
                  <a data-bs-toggle="collapse" href="#sidebarUsers" aria-expanded="false" aria-controls="sidebarUsers" class="side-nav-link">
                      <i class="uil-users-alt"></i>
                      <span> {{__('Users Management')}} </span>
                      <span class="menu-arrow"></span>
                  </a>
                  <div class="collapse" id="sidebarUsers">
                      <ul class="side-nav-second-level">
                          <li>
                              <a href="{{route('users.index')}}">
                                  <span> {{__('Users')}} </span>
                              </a>
                          </li>

                          <li>
                              <a href="{{route('roles.index')}}">
                                  <span> {{__('Roles')}} </span>
                              </a>
                          </li>

                          <li>
                              <a href="{{route('activity-logs.index')}}">
                                  <span> {{__('Activity Logs')}} </span>
                              </a>
                          </li>
                      </ul>
                  </div>
              </li>

              <li class="side-nav-item">
                  <a data-bs-toggle="collapse" href="#sidebarEmployees" aria-expanded="false" aria-controls="sidebarEmployees" class="side-nav-link">
                      <i class="uil-users-alt"></i>
                      <span> {{__('Employees Management')}} </span>
                      <span class="menu-arrow"></span>
                  </a>
                  <div class="collapse" id="sidebarEmployees">
                      <ul class="side-nav-second-level">
                          <li>
                              <a href="{{route('departments.index')}}">
                                  <span> {{__('Departments')}} </span>
                              </a>
                          </li>
                          <li>
                              <a href="{{route('employees.index')}}">
                                  <span> {{__('Employees')}} </span>
                              </a>
                          </li>
                      </ul>
                  </div>
              </li>


              <li class="side-nav-item">
                  <a data-bs-toggle="collapse" href="#sidebarLocation" aria-expanded="false" aria-controls="sidebarLocation" class="side-nav-link">
                      <i class="uil-map-marker"></i>
                      <span> {{__('Locations')}} </span>
                      <span class="menu-arrow"></span>
                  </a>
                  <div class="collapse" id="sidebarLocation">
                      <ul class="side-nav-second-level">
                          <li>
                              <a href="{{route('governorates.index')}}">
                                  <span>
                                      {{__('Governorates')}}
                                  </span>
                              </a>
                          </li>
                          <li>
                              <a href="{{route('cities.index')}}">
                                  <span>
                                      {{__('Cities')}}
                                  </span>
                              </a>
                          </li>
                          <li>
                              <a href="{{route('areas.index')}}">
                                  <span>
                                      {{__('Areas')}}
                                  </span>
                              </a>
                          </li>

                      </ul>
                  </div>
              </li>

              <li class="side-nav-item">
                  <a data-bs-toggle="collapse" href="#sidebarDonors" aria-expanded="false" aria-controls="sidebarDonors" class="side-nav-link">
                      <i class="uil-users-alt"></i>
                      <span> {{__('Donors')}} </span>
                      <span class="menu-arrow"></span>
                  </a>
                  <div class="collapse" id="sidebarDonors">
                      <ul class="side-nav-second-level">
                          <li>
                              <a href="{{route('call-types.index')}}">
                                  <span> {{__('Call Types')}} </span>
                              </a>
                          </li>
                          <li>
                              <a href="{{route('donors.index')}}">
                                  <span> {{__('Donors')}} </span>
                              </a>
                          </li>


                      </ul>
                  </div>
              </li>

              <li class="side-nav-item">
                  <a data-bs-toggle="collapse" href="#sidebarDonations" aria-expanded="false" aria-controls="sidebarDonations" class="side-nav-link">
                      <i class="uil-money-withdraw"></i>
                      <span> {{__('Donations')}} </span>
                      <span class="menu-arrow"></span>
                  </a>
                  <div class="collapse" id="sidebarDonations">
                      <ul class="side-nav-second-level">

                          <li>
                              <a href="{{route('donation-categories.index')}}">
                                  <span> {{__('Donation Categories')}} </span>
                              </a>
                          </li>
                          <li>
                              <a href="{{route('monthly-donations.index')}}">
                                  <span> {{__('Monthly Donations')}} </span>
                              </a>
                          </li>
                          <li>
                              <a href="{{route('monthly-donations.cancelled')}}">
                                  <span> {{__('Cancelled Monthly Donations')}} </span>
                              </a>
                          </li>
                          <li>
                              <a href="{{route('donations.index')}}">
                                  <span> {{__('Donations')}} </span>
                              </a>
                          </li>

                      </ul>
                  </div>
              </li>



          </ul>


          <!-- End Sidebar -->

          <div class="clearfix"></div>

      </div>
      <!-- Sidebar -left -->
  </div>
  <!-- Left Sidebar End -->