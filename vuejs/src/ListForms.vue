<template>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <div class="container mt-4">
      <h2 class="mb-4">Dynamic Form Generator</h2>
      
      <div class="mb-3">
        <label for="country" class="form-label">Select Country:</label>
        <select id="country" class="form-select" v-model="selectedCountry" @change="updateFields">
          <option value="default">Default</option>
          <option value="usa">USA</option>
          <option value="canada">Canada</option>
        </select>
      </div>
      
      <div class="row">
        <div v-for="(field, index) in fields" :key="index" class="field-container mb-3">
          <div class="row g-2">
            <div class="col-md-3">
              <select v-model="field.type" class="form-select" @change="checkDropdown(index)">
                <option value="text">Text</option>
                <option value="number">Number</option>
                <option value="date">Date</option>
                <option value="dropdown">Dropdown</option>
              </select>
            </div>
            <div class="col-md-3">
              <input v-if="field.type !== 'dropdown'" v-model="field.value" :type="field.type" :placeholder="`Field ${index + 1}`" :required="field.required" class="form-control">
              <div v-else>
                <input v-for="(option, optIndex) in field.options" :key="optIndex" v-model="field.options[optIndex]" placeholder="Option" class="form-control mb-1">
                <button @click="addOption(index)" class="btn btn-secondary btn-sm">Add Option</button>
              </div>
            </div>
            <div class="col-md-3">
              <select v-model="field.category" class="form-select">
                <option value="general">General</option>
                <option value="identity">Identity</option>
                <option value="bank">Bank</option>
              </select>
            </div>
            <div class="col-md-3">
              <div class="form-check">
                <input type="checkbox" v-model="field.required" class="form-check-input" :id="`requiredCheck${index}`">
                <label class="form-check-label" :for="`requiredCheck${index}`">Required</label>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="d-flex justify-content-between">
        <button @click="addField" class="btn btn-primary">Add Field</button>
        <button @click="removeField" class="btn btn-danger">Remove Field</button>
        <button @click="onSubmit" class="btn btn-success">Submit</button>
      </div>
  
      <h3 class="mt-4">Saved Forms</h3>
      <table class="table">
        <thead>
          <tr>
            <th>Country</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(form, formIndex) in savedForms" :key="formIndex">
            <td>{{ form.country }}</td>
            <td>
              <button @click="editForm(formIndex)" class="btn btn-warning btn-sm">Edit</button>
              <button @click="deleteForm(formIndex)" class="btn btn-danger btn-sm">Delete</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        selectedCountry: 'default',
        fields: [{ value: '', type: 'text', category: 'general', required: false, options: [] }],
        savedForms: [],
      };
    },
    methods: {
      addField() {
        this.fields.push({ value: '', type: 'text', category: 'general', required: false, options: [] });
      },
      removeField() {
        if (this.fields.length > 1) {
          this.fields.pop();
        }
      },
      updateFields() {
        this.fields = [{ value: '', type: 'text', category: 'general', required: false, options: [] }];
      },
      checkDropdown(index) {
        if (this.fields[index].type !== 'dropdown') {
          this.fields[index].options = [];
        }
      },
      addOption(index) {
        this.fields[index].options.push('');
      },
      onSubmit() {
        const formData = {
          country: this.selectedCountry,
          fields: this.fields,
        };
        this.savedForms.push(formData);
        console.log('Saved Forms:', this.savedForms);
        this.updateFields(); // Reset fields after submission
      },
      editForm(index) {
        const formToEdit = this.savedForms[index];
        this.selectedCountry = formToEdit.country;
        this.fields = formToEdit.fields;
        this.savedForms.splice(index, 1); // Remove the form from savedForms after editing
      },
      deleteForm(index) {
        this.savedForms.splice(index, 1);
      },
    },
  };
  </script>
  