<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Submit An Order</div>

                    <div class="card-body">
                        <div>
                            <div class="form-row align-items-center mb-4">
                                <div class="col-auto">
                                    <input
                                        type="text"
                                        class="form-control mb-2"
                                        placeholder="HMO Code"
                                        v-model="providerData.hmo_code"
                                    />
                                </div>
                                <div class="col-auto">
                                    <div class="input-group mb-2">
                                        <input
                                            type="text"
                                            class="form-control"
                                            placeholder="Provider Name"
                                            v-model="providerData.provider_name"
                                        />
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="input-group mb-2">
                                        <input
                                            type="date"
                                            class="form-control"
                                            placeholder="Date"
                                            v-model="providerData.date"
                                        />
                                    </div>
                                </div>
                            </div>

                            <div
                                v-for="(data, key) in formData.length"
                                :key="key"
                                class="col-12"
                            >
                                <div class="form-row align-items-center">
                                    <div class="col-4">
                                        <label
                                            class="sr-only"
                                            for="inlineFormInput"
                                            >Item</label
                                        >
                                        <input
                                            type="text"
                                            class="form-control mb-2"
                                            placeholder="Item"
                                            v-model="formData[key].item"
                                        />
                                    </div>
                                    <div class="col-3">
                                        <label
                                            class="sr-only"
                                            for="inlineFormInput"
                                            >Unit Price</label
                                        >
                                        <input
                                            type="text"
                                            class="form-control mb-2"
                                            placeholder="Unit Price"
                                            v-model="formData[key].unit_price"
                                        />
                                    </div>
                                    <div class="col-2">
                                        <label
                                            class="sr-only"
                                            for="inlineFormInput"
                                            >Qty</label
                                        >
                                        <input
                                            type="number"
                                            class="form-control mb-2"
                                            placeholder="Quantity"
                                            v-model="formData[key].qty"
                                        />
                                    </div>
                                    <div class="col-2">
                                        <label
                                            class="sr-only"
                                            for="inlineFormInput"
                                            >Sub Total</label
                                        >
                                        <input
                                            type="text"
                                            class="form-control mb-2"
                                            readonly
                                            :value="calculateSubTotal(key)"
                                        />
                                    </div>

                                    <div class="col-1">
                                        <button
                                            type="button"
                                            class="btn btn-primary mb-2 btn-sm"
                                            @click="removeRepeater(key)"
                                        >
                                            -
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-7">
                                    <button
                                        type="button"
                                        class="btn btn-primary mb-2 btn-sm"
                                        @click="addRepeater()"
                                    >
                                        +
                                    </button>
                                </div>
                                <div class="col-3">
                                    <label class="">Total</label>
                                    <input
                                        type="text"
                                        class="form-control mb-2"
                                        readonly
                                        :value="total"
                                    />
                                </div>
                            </div>

                            <div class="row border-top">
                                <div class="col-7 mt-3">
                                    <button
                                        type="button"
                                        class="btn btn-primary mb-2"
                                        @click="submit()"
                                    >
                                        Submit
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            formData: [],
            defaultData: {
                item: "",
                unit_price: null,
                qty: null,
                sub_total: null
            },
            providerData: {
                hmo_code: "",
                provider_name: "",
                date: ""
            },
            total: 0
        };
    },
    methods: {
        addRepeater() {
            this.formData.push({ ...this.defaultData });
        },
        removeRepeater(key) {
            this.formData.splice(key, 1);
        },
        calculateSubTotal(key) {
            this.calculateTotal();
            return (this.formData[key].sub_total =
                this.formData[key].unit_price * this.formData[key].qty);
        },
        calculateTotal() {
            let total = 0;
            this.formData.forEach(data => {
                total += data.unit_price * data.qty;
            });
            this.total = total;
        },
        async submit() {
            await axios
                .post("/api/create", {
                    order: this.formData,
                    total: this.total,
                    provider_data: this.providerData
                })
                .then(response => {
                    console.log(response);
                })
                .catch(error => {
                    console.error(error);
                });
        }
    },
    mounted() {
        this.addRepeater();
    }
};
</script>
