<template>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <div class="container mt-4">
        <h2 class="mb-4">Dynamic Form Generator</h2>

    <div v-if="list_display">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="mt-4">Saved Forms</h3>
                </div>
                <div class="col-md-6">
                    <button class="btn btn-success" @click="add_new_form"> add form</button>
                </div>
            </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Country</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(form, formIndex) in savedForms" :key="formIndex">
                    <td>{{ form.country.name }}</td>
                    <td>
                        <button @click="editForm(formIndex)" class="btn btn-warning btn-sm">Edit</button>
                        <button @click="deleteForm(formIndex)" class="btn btn-danger btn-sm">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>
     </div>
    <div v-else>
        <div class="row">
                <div class="col-md-6">
                    <h3 class="mt-4">Add new Form</h3>
                </div>
                <div class="col-md-6">
                    <button class="btn btn-success" @click="add_new_form">
                        Forms List
                    </button>
                </div>
            </div>
        <div class="mb-3">
            <label for="country" class="form-label">Select Country:</label>
            <v-select
  id="country"
  class="form-select"
  v-model="selectedCountry"
  :options="processedCountries"
  label="name"
  :reduce="country => country.id"
  @input="fetchForm"
  :custom-label="country => country.name + (country.disabled ? ' (Disabled)' : '')">
</v-select>



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
                    <div class="col-md-3" v-if="field.type == 'dropdown'">
        <!-- Display each option in an input field -->
        <div v-for="(option, optIndex) in  field.options" :key="optIndex" class="input-group mb-1">

            <input v-model="field.options[optIndex]" placeholder="Option" class="form-control">
            <button @click="removeOption(index, optIndex)" class="btn btn-danger btn-sm" v-if="field.options.length > 1">Delete</button>
        </div>
        <!-- Button to add a new option -->
        <button @click="addOption(index)" class="btn btn-secondary btn-sm">Add Option</button>
    </div>
                    <div class="col-md-3">
                        <select v-model="field.category" class="form-select">
                            <option value="general">General</option>
                            <option value="identity">Identity</option>
                            <option value="bank-related">Bank</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" v-model="field.is_required" class="form-check-input" :id="`requiredCheck${index}`">
                            <label class="form-check-label" :for="`requiredCheck${index}`">Required</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between">

            <button @click="addField" class="btn btn-primary">Add Field</button>
            <button @click="removeField" class="btn btn-danger" v-if="this.fields.length > 1">Remove Field</button>

            <button @click="onUpdate" class="btn btn-warning" v-if="is_update">Update</button>
            <button @click="onSubmit" class="btn btn-success" v-else>Submit</button>

            <button @click="discard_update" class="btn btn-danger" v-if="is_update">
                <i class="fa-solid fa-rotate-left"></i>
                Discard Update
            </button>

        </div>
    </div>



    </div>


</template>
<style>

body {
  font-family: "Source Sans Pro", "Helvetica Neue", Arial, sans-serif;
  text-rendering: optimizelegibility;
  -moz-osx-font-smoothing: grayscale;
  -moz-text-size-adjust: none;
}

#app {
  max-width: 60em;
  margin: 1em auto;
}

</style>
<script >
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css';

export default {
    components: {
    vSelect
  },

    data() {
        return {
            selectedCountry: null,
            countries: [],
            fields: [{ type: 'text', category: 'general', is_required: false, options: [''] }],
            savedForms: [],
            is_update : false,
            list_display : true,
            id : null,
            formToEdit: null,
        };
    },
    computed: {
    processedCountries() {
      return this.countries.map(country => ({
        ...country,
        disabled: country.form_count > 0,
      }));
    },
  },
    methods: {
        fetchCountries() {
            fetch('http://127.0.0.1:8000/api/form')
                .then(response => response.json())
                .then(data => {

                    this.savedForms=data.forms
                    this.countries = data.countries;
                    // console.log(this.savedForms)
                });
        },
        fetchForm() {
            if (!this.selectedCountry) return;

            fetch(`/api/form/${this.selectedCountry}`)
                .then(response => response.json())
                .then(() => {
                    this.fields = this.formToEdit.fields || [{ type: 'text', category: 'general', is_required: false, options: [''] }];
                    // this.fields = data.fields || [{ type: 'text', category: 'general', is_required: false, options: [''] }];
                });
        },
        addField() {
            this.fields.push({  type: 'text', category: 'general', is_required: false, options: [''] });
        },
        removeField() {
            if (this.fields.length > 1) {
                this.fields.pop();
            }
        },
        checkDropdown(index) {
            if (this.fields[index].type !== 'dropdown') {
                this.fields[index].options = [];
            }
        },
        addOption(index) {
            this.fields[index].options.push('');
        },
        removeOption(fieldIndex, optionIndex) {
      if (this.fields[fieldIndex].options.length > 1) {
        this.fields[fieldIndex].options.splice(optionIndex, 1);
      }

  },
        add_new_form(){
        this.list_display=!this.list_display;
        this.is_update=false;
        this.fetchCountries();
        },
        onSubmit() {
            const formData = {
                country_id: this.selectedCountry,
                fields: this.fields,
            };

            // console.log( JSON.stringify(formData))
            fetch('http://127.0.0.1:8000/api/form', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData),
            })
            .then(() => {
                // console.log(data)
                this.updateFields() ;
                this.fetchCountries();
                this.list_display=true;
            } )
            ;
        },
        onUpdate() {
            const formData = {
                form: this.formToEdit,
            };

            // console.log(formData)
            fetch(`http://127.0.0.1:8000/api/form/${this.formToEdit.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData),
            })
            .then(() => {
                this.updateFields();
                this.fetchCountries();
                this.list_display=true;
            } );

        },
        editForm(index) {
            this.id=this.savedForms[index].id
            this.formToEdit = this.savedForms[index];
            this.selectedCountry = this.formToEdit.country_id;
            this.fields = this.formToEdit.fields;
            // console.log( JSON.parse(this.fields[0].options));
            this.savedForms.splice(index, 1);
            this.is_update=true;
            this.list_display=false;
            this.is_update=true;
        },
        discard_update(){
            this.is_update=false;
            this.list_display=true;
            this.fetchCountries() ;
        },
        deleteForm(index) {
            const formToDelete = this.savedForms[index];
            // console.log(formToDelete.id)
            fetch(`http://127.0.0.1:8000/api/form/${formToDelete.id}`, {
                method: 'DELETE',
            }).then(() => {
                this.savedForms.splice(index, 1);
            });
        },
        updateFields() {
            this.fields = [{ type: 'text', category: 'general', is_required: false, options: [''] }];
        },
    },
    mounted() {
        this.fetchCountries();
    },
};
</script>
