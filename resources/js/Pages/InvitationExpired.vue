<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full text-center">
      <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
      </div>
      <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
        Invitation Expired
      </h2>
      <p class="mt-2 text-sm text-gray-600">
        This invitation link has expired or has already been used.
      </p>
      <p class="mt-4 text-sm text-gray-600">
        Please contact an administrator to request a new invitation.
      </p>
      <div class="mt-6">
        <a
          href="/"
          class="neema-btn neema-btn-primary"
        >
          Go to Home
        </a>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'InvitationExpired',
};
</script>
<style>
a:link {
  text-decoration: none;
}
</style>