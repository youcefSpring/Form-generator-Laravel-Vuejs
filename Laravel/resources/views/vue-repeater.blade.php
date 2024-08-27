<!DOCTYPE html>
<html>
<head>
    <title>Dynamic Form</title>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
</head>
<body>
    <div id="app">
        <form @submit.prevent="submitForm">
            <div v-for="(field, index) in fields" :key="index">
                <label :for="field.name">{{ field.label }}</label>
                <input
                    v-if="field.type === 'text'"
                    :type="field.type"
                    :name="field.name"
                    v-model="formData[field.name]"
                    :required="field.required"
                />
                <!-- Add other input types here -->
            </div>
            <button type="submit">Submit</button>
        </form>
    </div>

    <script>
        const { createApp } = Vue;

        createApp({
            data() {
                return {
                    fields: [], // Will be populated from API
                    formData: {}
                }
            },
            created() {
                fetch('/api/form-config')
                    .then(response => response.json())
                    .then(data => {
                        this.fields = data.fields;
                        // Initialize formData with empty values
                        this.fields.forEach(field => {
                            this.$set(this.formData, field.name, '');
                        });
                    });
            },
            methods: {
                submitForm() {
                    // Handle form submission
                    console.log(this.formData);
                }
            }
        }).mount('#app');
    </script>
</body>
</html>
