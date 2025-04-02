<template>
    <div class="container mt-4">
      <h1 class="mb-4 fw-bold">Welcome to the Dashboard</h1>
      <div class="row g-4">
        <!-- Queue Overview -->
        <div class="col-md-6 col-lg-3">
          <div class="card border-primary shadow-sm">
            <div class="card-body d-flex align-items-center justify-content-between">
              <div>
                <h5 class="card-title text-primary">Current Queue</h5>
                <h2 class="fw-bold">{{ queueCount }}</h2>
                <p class="text-muted mb-0">People waiting</p>
              </div>
              <font-awesome-icon :icon="icons.faUsers" class="text-primary display-4" />
            </div>
          </div>
        </div>

        <!-- Total Bookings -->
        <div class="col-md-6 col-lg-3">
          <div class="card border-success shadow-sm">
            <div class="card-body d-flex align-items-center justify-content-between">
              <div>
                <h5 class="card-title text-success">Total Bookings</h5>
                <h2 class="fw-bold">{{ totalBookings }}</h2>
                <p class="text-muted mb-0">This month</p>
              </div>
              <font-awesome-icon :icon="icons.faCalendarCheck" class="text-success display-4" />
            </div>
          </div>
        </div>

        <!-- Completed Services -->
        <div class="col-md-6 col-lg-3">
          <div class="card border-warning shadow-sm">
            <div class="card-body d-flex align-items-center justify-content-between">
              <div>
                <h5 class="card-title text-warning">Completed Services</h5>
                <h2 class="fw-bold">{{ completedServices }}</h2>
                <p class="text-muted mb-0">Satisfied customers</p>
              </div>
              <font-awesome-icon :icon="icons.faCheckCircle" class="text-warning display-4" />
            </div>
          </div>
        </div>

        <!-- Revenue -->
        <div class="col-md-6 col-lg-3">
          <div class="card border-info shadow-sm">
            <div class="card-body d-flex align-items-center justify-content-between">
              <div>
                <h5 class="card-title text-info">Revenue</h5>
                <h2 class="fw-bold">R {{ revenue }}</h2>
                <p class="text-muted mb-0">This month</p>
              </div>
              <font-awesome-icon :icon="icons.faMoneyBillWave" class="text-info display-4" />
            </div>
          </div>
        </div>
      </div>
      <div class="row mt-4">
        <div class="col-md-6">
          <div class="card shadow-sm">
            <div class="card-body">
              <h5 class="card-title">Live Queue</h5>
              <!-- Live queue visualization goes here -->
            </div>
          </div>
        </div>
        </div>
    </div>

</template>

<script>
import DashboardLayout from "../../../Layouts/DashboardLayout.vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faUsers, faCalendarCheck, faCheckCircle, faMoneyBillWave } from "@fortawesome/free-solid-svg-icons";

export default {
  components: {  FontAwesomeIcon },
  data() {
    return {
      queueCount: 8, // Placeholder number of people in queue
      totalBookings: 120, // Total bookings in a month
      completedServices: 98, // Services completed
      revenue: 7500, // Revenue for the month in Rands
      icons: { faUsers, faCalendarCheck, faCheckCircle, faMoneyBillWave },
    };
  },
};
</script>
