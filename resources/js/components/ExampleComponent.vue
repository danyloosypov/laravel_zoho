<template>
    <div>
      <h2>Create Deal and Account</h2>
  
      <form @submit.prevent="createRecords">
        <div>
          <label for="dealName">Deal Name:</label>
          <input type="text" id="dealName" v-model="dealName" required>
        </div>
  
        <div>
          <label for="dealStage">Deal Stage:</label>
          <input type="text" id="dealStage" v-model="dealStage" required>
        </div>
  
        <div>
          <label for="accountName">Account Name:</label>
          <input type="text" id="accountName" v-model="accountName" required>
        </div>
  
        <div>
          <label for="accountWebsite">Account Website:</label>
          <input type="text" id="accountWebsite" v-model="accountWebsite" required>
        </div>
  
        <div>
          <label for="accountPhone">Account Phone:</label>
          <input type="text" id="accountPhone" v-model="accountPhone" required>
        </div>
  
        <div>
          <button type="submit">Create Records</button>
        </div>
      </form>
  
      <div v-if="successMessage" class="success-message">{{ successMessage }}</div>
      <div v-if="errorMessage" class="error-message">{{ errorMessage }}</div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        dealName: '',
        dealStage: '',
        accountName: '',
        accountWebsite: '',
        accountPhone: '',
        successMessage: '',
        errorMessage: ''
      };
    },
    methods: {
      createRecords() {
        // Perform form validation
        if (!this.validateForm()) {
          return;
        }
  
        // Make an API call to create the deal and account records
        axios.post('/create-records', {
          dealName: this.dealName,
          dealStage: this.dealStage,
          accountName: this.accountName,
          accountWebsite: this.accountWebsite,
          accountPhone: this.accountPhone
        })
        .then(response => {
          // Handle the response
          if (response.status === 201) {
            this.successMessage = response.data.message;
          } else {
            this.errorMessage = response.data.error;
          }
  
          // Clear form data
          this.dealName = '';
          this.dealStage = '';
          this.accountName = '';
          this.accountWebsite = '';
          this.accountPhone = '';
        })
        .catch(error => {
          // Handle the error
          this.errorMessage = error;
          console.error(error);
        });
      },
      validateForm() {
        // Perform validation on form fields and display error messages if needed
        if (!this.dealName || !this.dealStage || !this.accountName || !this.accountWebsite || !this.accountPhone) {
          this.errorMessage = 'Please fill in all fields.';
          return false;
        }
  
        // Additional validation logic can be added here
  
        return true;
      }
    }
  };
  </script>
  