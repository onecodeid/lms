<template>
    <v-row>

        <v-col cols="12">
            <v-row no-gutters>
                <v-spacer></v-spacer>
                <v-col cols="12" md="4" lg="5" class="d-flex">
                    <!-- <v-sheet class="pa-12" color="grey lighten-3"> -->
                    <v-sheet :elevation="0" class="mx-auto fill-height py-10 px-10 grow" height="auto" width="100%">
                        <h2 class="my-5">Pendaftaran Kursus</h2>
                        <p>Tempor erat elitr rebum at clita. Diam dolor diam ipsum sit. Aliqu diam amet diam et eos.
                            Clita erat ipsum et lorem et sit.</p>
                        <p>Tempor erat elitr rebum at clita. Diam dolor diam ipsum sit. Aliqu diam amet diam et eos.
                            Clita erat ipsum et lorem et sit, sed stet lorem sit clita duo justo magna dolore erat amet
                        </p>
                    </v-sheet>
                    <!-- </v-sheet> -->
                </v-col>
                <v-col cols="12" md="8" lg="7">
                    <v-card :elevation="0" class="fill-height pa-10">
                        <v-card-text class="pa-0">
                            <v-form ref="form" v-model="valid" @submit.prevent="submitForm">
                                <v-select :items="payments" item-value="M_PaymentTypeID" item-text="M_PaymentTypeName"
                                    label="Pilih Metode pembayaran" v-model="selected_payment" disabled></v-select>

                                <!-- <v-text-field
                            label="Masukkan Coupon jika ada"
                            v-model="coupon_code"
                            @change="check_coupon"
                            :readonly="coupon_set"
                            :clearable="coupon_set"
                            @click:clear="coupon_clear"
                            v-show="!coupon_set"
                            outlined
                        ></v-text-field> -->

                                <v-textarea label="Catatan Tambahan" rows="2" outlined v-model="order_note">
                                </v-textarea>


                                <v-row>
                                    <v-col cols="6">Total Biaya Kursus</v-col>
                                    <v-col cols="6" class="text-right">Rp {{ one_money(total_price) }}</v-col>
                                </v-row>

                                <v-row><v-divider class="mt-2 mb-2"></v-divider></v-row>


                                <v-row row wrap>
                                    <v-col cols="6" v-show="coupon_set">
                                        Kupon : <b>{{ coupon_code }}</b> <a href="javascript:;"
                                            @click="coupon_clear"><v-icon small>clear</v-icon></a>
                                    </v-col>
                                    <v-col cols="6" class="text-xs-right red--text" v-show="coupon_set">
                                        - Rp {{ one_money(coupon_amounts) }}
                                    </v-col>
                                    <v-col cols="6" class="caption red--text" v-show="false">
                                        {{ coupon_note }}
                                    </v-col>
                                </v-row>

                                <v-row row wrap pt-2>
                                    <v-col cols="6" class=""><b>Total Belanja</b></v-col>
                                    <v-col cols="6" class="text-right">Rp <b>{{ one_money(grand_total_price)
                                            }}</b></v-col>
                                </v-row>

                                <v-row row wrap>
                                    <v-divider class="mt-2 mb-2"></v-divider>
                                </v-row>

                                <v-row row wrap pt-2>
                                    <v-col cols="8" class=""><b>Total Yang Harus Dibayar</b></v-col>
                                    <v-col cols="4" class="text-right">Rp <b>{{ one_money(final_total_price)
                                            }}</b></v-col>

                                    <v-col class="text-xs-center d-flex" mt-5 cols="12">
                                        <v-spacer></v-spacer>
                                        <v-btn type="button" @click="step--" class="mr-2">Kembali</v-btn>
                                        <v-btn color="primary" type="submit">Simpan</v-btn>
                                    </v-col>
                                </v-row>
                            </v-form>


                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-col>
    </v-row>
</template>

<script>
module.exports = {
    data() {
        return {
            e1: 2,
            coupon_set: false,

            total_price: 100000,
            coupon_amounts: 0,
            coupon_note: '',
            grand_total_price: 100000,
            final_total_price: 100000,

            valid: false
        }
    },

    components: {
    },

    computed: {
        ...Vuex.mapState('register', ['payments']),
        __s() { return this.$store.state.register },

        coupon_code: {
            get() { return this.__s.coupon_code },
            set(v) { this.__c("coupon_code", v) }
        },

        order_note: {
            get() { return this.__s.order_note },
            set(v) { this.__c("order_note", v) }
        },

        selected_payment: {
            get() { return this.__s.selected_payment },
            set(v) { this.setObject('selected_payment', v) }
        },

        step: {
            get () { return this.$store.state.register.step },
            set (v) { this.$store.commit('register/set_object', ['step', v]) } }
    },

    methods: {
        __c(a, b) { this.$store.commit('register/set_object', [a, b]) },

        setObject(a, b) {
            this.$store.commit('register/set_object', [a, b])
        },

        one_money(x) {
            return window.one_money(x)
        },

        check_coupon() {
            return
        },

        coupon_clear() {
            this.coupon_code = ""
        },

        submitForm() {
            // Trigger form validation
            if (this.$refs.form.validate()) {
                // Perform your custom action here
                // console.log('Form Data:', this.formData);
                // alert('Form submitted!');

                this.$store.dispatch("register/save").then(x => {
                    this.__c("invoiceNumber", x.invoice_number)
                    this.$store.dispatch("register/searchInvoice").then(y=>{
                        this.__c("selected_order", y.records[0])
                        this.step++
                    })
                })
                // Reset the form if needed
                // this.$refs.form.reset();
            }
        }
    },

    mounted() {
        this.$store.dispatch("register/search_payment")
    }
}
</script>