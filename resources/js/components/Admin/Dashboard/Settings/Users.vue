<template>
  <div class="">
    <div class="sm:flex sm:items-center">
      <div class="sm:flex-auto">
        <h2 class="text-lg sm:text-xl fw-bold text-gray-900">Users Management</h2>
        <p class="mt-2 text-sm text-gray-700">
          Invite new users to manage your application. They will receive an email with a
          link to complete their registration.
        </p>
      </div>
      <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
        <button
          @click="showInviteModal = true"
          type="button"
          class="penda-btn penda-btn-primary w-full sm:w-auto inline-flex items-center justify-center px-3 py-2 text-sm font-semibold text-white"
        >
          Invite User
        </button>
      </div>
    </div>

    <hr>

    <!-- Pending Invitations -->
    <div class="mt-8">
      <h3 class="text-base fw-bold text-gray-900">Pending Invitations</h3>
      
      <!-- Desktop Table View -->
      <div class="mt-4 hidden sm:block overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-300">
          <thead class="bg-gray-50">
            <tr>
              <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">
                Email
              </th>
              <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                Invited By
              </th>
              <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                Expires
              </th>
              <th class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                <span class="sr-only">Actions</span>
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 bg-white">
            <tr v-if="loading">
              <td colspan="4" class="py-4 text-center text-sm text-gray-500">
                Loading...
              </td>
            </tr>
            <tr v-else-if="invitations.length === 0">
              <td colspan="4" class="py-4 text-center text-sm text-gray-500">
                No pending invitations
              </td>
            </tr>
            <tr v-else v-for="invitation in invitations" :key="invitation.id">
              <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900">
                {{ invitation.email }}
              </td>
              <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                {{ invitation.inviter.name }}
              </td>
              <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                {{ formatDate(invitation.expires_at) }}
              </td>
              <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                <button
                  @click="cancelInvitation(invitation)"
                  class="text-red-600 hover:text-red-900"
                >
                  Cancel
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Mobile Card View -->
      <div class="mt-4 sm:hidden">
        <div v-if="loading" class="text-center py-8 text-sm text-gray-500">
          Loading...
        </div>
        <div v-else-if="invitations.length === 0" class="text-center py-8 text-sm text-gray-500">
          No pending invitations
        </div>
        <div v-else class="space-y-4">
          <div
            v-for="invitation in invitations"
            :key="invitation.id"
            class="bg-white shadow rounded-lg p-4 ring-1 ring-black ring-opacity-5"
          >
            <div class="flex justify-between items-start mb-2">
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 break-all">
                  {{ invitation.email }}
                </p>
                <p class="text-xs text-gray-500 mt-1">
                  Invited by {{ invitation.inviter.name }}
                </p>
              </div>
            </div>
            <div class="flex justify-between items-center mt-3 pt-3 border-t border-gray-200">
              <p class="text-xs text-gray-500">
                Expires {{ formatDate(invitation.expires_at) }}
              </p>
              <button
                @click="cancelInvitation(invitation)"
                class="text-sm font-medium text-red-600 hover:text-red-900"
              >
                Cancel
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <hr class="block sm:hidden">

    <!-- Active Users -->
    <div class="mt-8">
      <h3 class="text-base fw-bold text-gray-900">Active Users</h3>
      
      <!-- Desktop Table View -->
      <div class="mt-4 hidden sm:block overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-300">
          <thead class="bg-gray-50">
            <tr>
              <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">
                Name
              </th>
              <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                Email
              </th>
              <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                Joined
              </th>
              <th class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                <span class="sr-only">Actions</span>
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 bg-white">
            <tr v-if="loadingUsers">
              <td colspan="4" class="py-4 text-center text-sm text-gray-500">
                Loading...
              </td>
            </tr>
            <tr v-else-if="users.length === 0">
              <td colspan="4" class="py-4 text-center text-sm text-gray-500">
                No users found
              </td>
            </tr>
            <tr v-else v-for="user in users" :key="user.id">
              <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900">
                {{ user.name }}
              </td>
              <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                {{ user.email }}
              </td>
              <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                {{ formatDate(user.created_at) }}
              </td>
              <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                <button
                  @click="deleteUser(user)"
                  class="text-red-600 hover:text-red-900"
                  :disabled="user.is_current_user"
                  :class="{ 'opacity-50 cursor-not-allowed': user.is_current_user }"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Mobile Card View -->
      <div class="mt-4 sm:hidden">
        <div v-if="loadingUsers" class="text-center py-8 text-sm text-gray-500">
          Loading...
        </div>
        <div v-else-if="users.length === 0" class="text-center py-8 text-sm text-gray-500">
          No users found
        </div>
        <div v-else class="space-y-4">
          <div
            v-for="user in users"
            :key="user.id"
            class="bg-white shadow rounded-lg p-4 ring-1 ring-black ring-opacity-5"
          >
            <div class="flex justify-between items-start mb-2">
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900">
                  {{ user.name }}
                </p>
                <p class="text-xs text-gray-500 mt-1 break-all">
                  {{ user.email }}
                </p>
              </div>
            </div>
            <div class="flex justify-between items-center mt-3 pt-3 border-t border-gray-200">
              <p class="text-xs text-gray-500">
                Joined {{ formatDate(user.created_at) }}
              </p>
              <button
                @click="deleteUser(user)"
                class="text-sm font-medium text-red-600 hover:text-red-900"
                :disabled="user.is_current_user"
                :class="{ 'opacity-50 cursor-not-allowed': user.is_current_user }"
              >
                Delete
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Invite User Modal -->
    <InviteUserModal
      :show="showInviteModal"
      :is-submitting="isSubmitting"
      @close="closeInviteModal"
      @submit="sendInvitation"
      ref="inviteModal"
    />
  </div>
</template>

<script>
import axios from 'axios';
import InviteUserModal from './InviteUserModal.vue';

export default {
  name: "Users",
  components: {
    InviteUserModal
  },
  data() {
    return {
      invitations: [],
      users: [],
      loading: false,
      loadingUsers: false,
      showInviteModal: false,
      isSubmitting: false
    };
  },
  mounted() {
    this.fetchInvitations();
    this.fetchUsers();
  },
  methods: {
    async fetchInvitations() {
      this.loading = true;
      try {
        const response = await axios.get('/api/invitations');
        this.invitations = response.data;
      } catch (error) {
        console.error('Error fetching invitations:', error);
      } finally {
        this.loading = false;
      }
    },
    async fetchUsers() {
      this.loadingUsers = true;
      try {
        const response = await axios.get('/api/users');
        this.users = response.data;
      } catch (error) {
        console.error('Error fetching users:', error);
      } finally {
        this.loadingUsers = false;
      }
    },
    async sendInvitation(formData) {
      this.isSubmitting = true;

      try {
        const response = await axios.post('/api/invitations', {
          email: formData.email,
        });
        
        this.invitations.unshift(response.data.invitation);
        this.closeInviteModal();
        alert('Invitation sent successfully!');
      } catch (error) {
        if (error.response?.status === 422) {
          // Pass server validation errors to the modal
          this.$refs.inviteModal.setErrors(error.response.data.errors);
        } else {
          alert('Failed to send invitation. Please try again.');
        }
      } finally {
        this.isSubmitting = false;
      }
    },
    async cancelInvitation(invitation) {
      if (!confirm(`Are you sure you want to cancel the invitation for ${invitation.email}?`)) {
        return;
      }

      try {
        await axios.delete(`/api/invitations/${invitation.id}`);
        this.invitations = this.invitations.filter(i => i.id !== invitation.id);
      } catch (error) {
        alert('Failed to cancel invitation. Please try again.');
      }
    },
    async deleteUser(user) {
      if (user.is_current_user) {
        alert('You cannot delete your own account.');
        return;
      }

      if (!confirm(`Are you sure you want to delete ${user.name}? This action cannot be undone.`)) {
        return;
      }

      try {
        await axios.delete(`/api/users/${user.id}`);
        this.users = this.users.filter(u => u.id !== user.id);
        alert('User deleted successfully!');
      } catch (error) {
        alert('Failed to delete user. Please try again.');
      }
    },
    closeInviteModal() {
      this.showInviteModal = false;
    },
    formatDate(date) {
      return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
      });
    },
  },
};
</script>