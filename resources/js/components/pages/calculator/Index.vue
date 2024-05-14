<template>
    <v-card class="mt-5 mx-auto" width="800">
        <template v-slot:title>
            <span class="font-weight-black">{{ $t('calculator.card_title') }}</span>
        </template>

        <v-card-text class="bg-surface-light pt-4">
            <v-container>
                <v-row>
                    <v-col cols="12">
                        <v-file-input
                            ref="fileInput"
                            :label="$t('calculator.input_csv')"
                            accept=".csv, text/csv"
                            variant="underlined"
                            @change="handleFileUpload"
                            clearable>
                        </v-file-input>
                    </v-col>
                </v-row>

                <v-row justify="center">
                    <v-col cols="6">
                        <v-btn block @click="submitFile">{{ $t('global.submit') }}</v-btn>
                    </v-col>
                </v-row>
            </v-container>

            <div v-if="result !== null">
                <p class="font-weight-thin text-center mt-10 mb-5">
                    {{ $t('global.result') }}: {{ result }}
                </p>
            </div>

            <div v-if="errorMessage">
                <p class="font-weight-thin text-center mt-10 mb-5 text-danger">
                    {{ errorMessage }}
                </p>
            </div>
        </v-card-text>
    </v-card>
</template>

<script>
import axios from 'axios';

export default {
    name: 'CommissionCalculator',
    data() {
        return {
            file: null,
            result: null,
            errorMessage: ''
        };
    },
    methods: {
        handleFileUpload(event) {
            this.file = event.target.files[0];  // In MVP you can only work with single file at a time
        },
        async submitFile() {
            if (!this.file) {
                alert('Please select a file first.');
                return;
            }

            const formData = new FormData();
            formData.append('file', this.file);

            try {
                const response = await axios.post('/upload', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });
                this.result = response.data;
                this.errorMessage = '';
            } catch (error) {
                console.error('Error uploading file:', error);
                this.errorMessage = 'Failed to upload file: ' + (error.response?.data?.message || error.message);
            }
        }
    }
};
</script>
