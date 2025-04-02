<template>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Profile Update Form -->
         <h2>Profile</h2>
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
          <div class="max-w-xl">
            <UpdateProfileForm />
          </div>
        </div>
  
        <!-- Password Update Form -->
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
          <div class="max-w-xl">
            <UpdatePasswordForm />
          </div>
        </div>
  
        <!-- Delete User Form -->
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
          <div class="max-w-xl">
            <DeleteUserForm />
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import UpdateProfileForm from "@/components/Admin/Dashboard/Profile/UpdateProfileForm.vue";
  import UpdatePasswordForm from "@/components/Admin/Dashboard/Profile/UpdatePasswordForm.vue";
  import DeleteUserForm from "@/components/Admin/Dashboard/Profile/DeleteUserForm.vue";
  
  export default {
    components: {
      UpdateProfileForm,
      UpdatePasswordForm,
      DeleteUserForm,
    },
  };
  </script>
  