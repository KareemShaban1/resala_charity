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
                  <a href="{{route('dashboard.index')}}" class="side-nav-link">
                      <i class="uil-home-alt"></i>
                      <!-- <span class="badge bg-success float-end">4</span> -->
                      <span>
                          {{__('Dashboard')}}
                      </span>
                  </a>
              </li>

              <li class="side-nav-item">
                  <a href="{{route('backups.index')}}" class="side-nav-link">
                      <i class="uil-home-alt"></i>
                      <span>
                          {{__('Backups')}}
                      </span>
                  </a>
              </li>

              <li class="side-nav-item">
                  <a data-bs-toggle="collapse" href="#sidebarEventsReport" aria-expanded="false" aria-controls="sidebarEventsReport" class="side-nav-link">
                      <i class="uil-money-withdraw"></i>
                      <span> {{__('Events')}} </span>
                      <span class="menu-arrow"></span>
                  </a>
                  <div class="collapse" id="sidebarEventsReport">
                      <ul class="side-nav-second-level">
                          <!-- @can('view collecting lines') -->
                          <li>
                              <a href="{{route('events.index')}}">
                                  <span> {{__('Events')}} </span>
                              </a>
                          </li>
                          <li>
                              <a href="{{route('calendar')}}">
                                  <span> {{__('Calendar')}} </span>
                              </a>
                          </li>
                          <!-- @endcan -->

                      </ul>
                  </div>
              </li>

              <li class="side-nav-item">
                  <a data-bs-toggle="collapse" href="#sidebarUsers" aria-expanded="false" aria-controls="sidebarUsers" class="side-nav-link">
                      <i class="uil-users-alt"></i>
                      <span> {{__('Users Management')}} </span>
                      <span class="menu-arrow"></span>
                  </a>
                  <div class="collapse" id="sidebarUsers">
                      <ul class="side-nav-second-level">
                          @can('view users')
                          <li>
                              <a href="{{route('users.index')}}">
                                  <span> {{__('Users')}} </span>
                              </a>
                          </li>
                          @endcan

                          @can('view roles')
                          <li>
                              <a href="{{route('roles.index')}}">
                                  <span> {{__('Roles')}} </span>
                              </a>
                          </li>
                          @endcan

                          @can('view activity logs')
                          <li>
                              <a href="{{route('activity-logs.index')}}">
                                  <span> {{__('Activity Logs')}} </span>
                              </a>
                          </li>
                          @endcan
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
                          @can('view departments')
                          <li>
                              <a href="{{route('departments.index')}}">
                                  <span> {{__('Departments')}} </span>
                              </a>
                          </li>
                          @endcan

                          @can('view employees')
                          <li>
                              <a href="{{route('employees.index')}}">
                                  <span> {{__('Employees')}} </span>
                              </a>
                          </li>
                          @endcan
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
                          @can('view governorates')
                          <li>
                              <a href="{{route('governorates.index')}}">
                                  <span>
                                      {{__('Governorates')}}
                                  </span>
                              </a>
                          </li>
                          @endcan

                          @can('view cities')
                          <li>
                              <a href="{{route('cities.index')}}">
                                  <span>
                                      {{__('Cities')}}
                                  </span>
                              </a>
                          </li>
                          @endcan

                          @can('view areas')
                          <li>
                              <a href="{{route('areas.index')}}">
                                  <span>
                                      {{__('Areas')}}
                                  </span>
                              </a>
                          </li>
                          @endcan

                          @can('view areas groups')
                          <li>
                              <a href="{{route('areas-groups.index')}}">
                                  <span>
                                      {{__('Areas Groups')}}
                                  </span>
                              </a>
                          </li>
                          @endcan

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
                          @can('view call types')
                          <li>
                              <a href="{{route('call-types.index')}}">
                                  <span> {{__('Call Types')}} </span>
                              </a>
                          </li>
                          @endcan

                          @can('view donors')
                          <li>
                              <a href="{{route('donors.index')}}">
                                  <span> {{__('Donors')}} </span>
                              </a>
                          </li>
                          @endcan
                          @can('view donors')
                          <li>
                              <a href="{{route('donors.random')}}">
                                  <span> {{__('Random Donors')}} </span>
                              </a>
                          </li>
                          @endcan

                      </ul>
                  </div>
              </li>

              <li class="side-nav-item">
                  <a data-bs-toggle="collapse" href="#sidebarMonthlyForms" aria-expanded="false" aria-controls="sidebarMonthlyForms" class="side-nav-link">
                      <i class="uil-money-withdraw"></i>
                      <span> {{__('Monthly Forms')}} </span>
                      <span class="menu-arrow"></span>
                  </a>
                  <div class="collapse" id="sidebarMonthlyForms">
                      <ul class="side-nav-second-level">
                          @can('view monthly forms')
                          <li>
                              <a href="{{route('monthly-forms.index')}}">
                                  <span> {{__('Monthly Forms')}} </span>
                              </a>
                          </li>
                          @endcan

                          @can('view monthly forms')
                          <li>
                              <a href="{{route('monthly-forms.cancelled')}}">
                                  <span> {{__('Cancelled Monthly Forms')}} </span>
                              </a>
                          </li>
                          @endcan

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

                          @can('view donation categories')
                          <li>
                              <a href="{{route('donation-categories.index')}}">
                                  <span> {{__('Donation Categories')}} </span>
                              </a>
                          </li>
                          @endcan


                          @can('view donations')
                          <li>
                              <a href="{{route('donations.index')}}">
                                  <span> {{__('Donations')}} </span>
                              </a>
                          </li>
                          @endcan

                          @can('view donations')
                          <li>
                              <a href="{{route('donations.monthly-donations')}}">
                                  <span> {{__('Monthly Donations')}} </span>
                              </a>
                          </li>
                          @endcan

                          @can('view donations')
                          <li>
                              <a href="{{route('donations.gathered-donations')}}">
                                  <span> {{__('Gathered Donations')}} </span>
                              </a>
                          </li>
                          @endcan




                      </ul>
                  </div>
              </li>

              <li class="side-nav-item">
                  <a data-bs-toggle="collapse" href="#sidebarCollectingLines" aria-expanded="false" aria-controls="sidebarCollectingLines" class="side-nav-link">
                      <i class="uil-money-withdraw"></i>
                      <span> {{__('Collecting Lines')}} </span>
                      <span class="menu-arrow"></span>
                  </a>
                  <div class="collapse" id="sidebarCollectingLines">
                      <ul class="side-nav-second-level">
                          @can('view collecting lines')
                          <li>
                              <a href="{{route('collecting-lines.addCollectingLines')}}">
                                  <span> {{__('Add Collecting Lines')}} </span>
                              </a>
                          </li>
                          @endcan
                          @can('view collecting lines')
                          <li>
                              <a href="{{route('collecting-lines.index')}}">
                                  <span> {{__('All Collecting Lines')}} </span>
                              </a>
                          </li>
                          @endcan
                      </ul>
                  </div>
              </li>


              <li class="side-nav-item">
                  <a data-bs-toggle="collapse" href="#sidebarMonthlyFormsReport" aria-expanded="false" aria-controls="sidebarMonthlyFormsReport" class="side-nav-link">
                      <i class="uil-money-withdraw"></i>
                      <span> {{__('Reports')}} </span>
                      <span class="menu-arrow"></span>
                  </a>
                  <div class="collapse" id="sidebarMonthlyFormsReport">
                      <ul class="side-nav-second-level">
                          <li>
                              <a href="{{route('monthly-forms-report.index')}}">
                                  <span> {{__('Monthly Forms Reports')}} </span>
                              </a>
                          </li>
                          <li>
                              <a href="{{route('donor-report.donorActivities')}}">
                                  <span> {{__('Donor Activities Reports')}} </span>
                              </a>
                          </li>
                          <li>
                              <a href="{{route('donor-report.donor-random-calls')}}">
                                  <span> {{__('Donor Random Calls Reports')}} </span>
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