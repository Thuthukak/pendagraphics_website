<template>
  <div>
    <!-- booking text on left. book button on right -->
    <div class="d-flex justify-content-between align-items-center">
      <h2 class="mb-4">Bookings</h2>
      <button class="btn btn-primary" @click="openBookingModal">Book</button>
    </div>
    

    <table class="table table-bordered mt-4">
      <thead>
        <tr>
          <th>Reference</th>
          <th>Client</th>
          <th>Service</th>
          <th>Barber</th>
          <th>Duration (mins)</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(booking, index) in bookings" :key="booking.id">
          <td>{{ booking.reference }}</td>
          <td>{{ booking.client.name }}</td>
          <td>{{ booking.service.name }}</td>
          <td>{{ booking.barber.name }}</td>
          <td>{{ booking.service.duration }}</td>
          <td>
            <span :class="statusClass(booking.status)">{{ booking.status }}</span>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <BookingModal :isOpen="showBookingModal" @close="closeBookingModal" />
</template>

<script>
import axios from "axios";
import BookingModal from "@/components/Home/BookingModal.vue";
export default {
  components: { BookingModal },
  data() {
    return {
      bookings: [],
      showBookingModal: false
    };
  },
  mounted() {
    this.fetchBookings();
  },
  methods: {
    async fetchBookings() {
      try {
        const response = await axios.get("/api/bookings/data");
        this.bookings = response.data;
      } catch (error) {
        console.error("Error fetching bookings:", error);
      }
    },
    statusClass(status) {
      switch (status) {
        case "in-progress":
          return "badge bg-warning text-dark";
        case "queued":
          return "badge bg-primary";
        case "completed":
          return "badge bg-success";
        case "cancelled":
          return "badge bg-danger";
        default:
          return "badge bg-secondary";
      }
    },
    openBookingModal() {
      this.showBookingModal = true;
    },
    closeBookingModal() {
      this.showBookingModal = false;
    },
  },
};
</script>

<style>
/* You can add additional custom styling if needed */
</style>
