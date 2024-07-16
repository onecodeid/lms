<template>
    <v-container>
        <v-row no-gutters>
                <v-spacer></v-spacer>
                <v-col cols="12" md="4" lg="5" class="d-flex">
                    <!-- <v-sheet class="pa-12" color="grey lighten-3"> -->
                    <v-sheet :elevation="0" class="mx-auto fill-height py-10 px-10 grow" height="auto" width="100%">
                        <h2 class="my-5">Konfirmasi Pembayaran</h2>
                        <p>Tempor erat elitr rebum at clita. Diam dolor diam ipsum sit. Aliqu diam amet diam et eos. Clita erat ipsum et lorem et sit.</p>
                        <p>Tempor erat elitr rebum at clita. Diam dolor diam ipsum sit. Aliqu diam amet diam et eos. Clita erat ipsum et lorem et sit, sed stet lorem sit clita duo justo magna dolore erat amet</p>
                    </v-sheet>
                    <!-- </v-sheet> -->
                </v-col>
                <v-col cols="12" md="8" lg="7">
                    <v-card :elevation="0" class="fill-height pa-10">
                        <v-card-text class="pa-0">
                            <v-form ref="form" v-model="validForm" @submit.prevent="submitForm">
                                <v-row no-gutters>
                                    <v-col cols="4" class="pr-2">
                                        <v-text-field name="payment_code" label="Nomor Invoice" id="payment_code" v-model="invoiceNumber" @change="searchInvoice"
                                            required></v-text-field>
                                    </v-col>
                                    <v-col cols="4">
                                        <v-text-field name="invoice_total" label="Nominal Invoice" id="payment_code" readonly :value="invoiceTotal" prefix="Rp"></v-text-field>

                                    
                                    </v-col><v-col cols="4">&nbsp;</v-col>

                                    <v-col cols="6" class="pr-2">
                                        <v-text-field name="payment_amount" label="Nominal Pembayaran" id="payment_amount" v-model="paymentAmount"
                                            required prefix="Rp" :disabled="!valid"></v-text-field>
                                    </v-col>

                                    <v-col cols="3">
                                        <v-text-field name="payment_date" label="Tanggal Pembayaran" id="payment_code" v-model="paymentDate"
                                            required :disabled="!valid"></v-text-field>
                                    </v-col>
                                    <v-col cols="3" class="pl-2">
                                        <v-text-field name="payment_time" label="Jam Pembayaran" id="payment_time" v-model="paymentTime"
                                            required :disabled="!valid"></v-text-field>
                                    </v-col>

                                    <v-col cols="12" class="pt-10 pb-5">
                                        <label class="cyan--text">Data Bank</label>
                                        <v-divider></v-divider>
                                    </v-col>

                                    <v-col cols="6">
                                        <v-autocomplete
                                            :items="banks"
                                            item-text="M_BankName"
                                            item-value="M_BankID"
                                            return-object
                                            v-model="selectedBank"
                                            label="Bank Pengirim"
                                            placeholder="-- Pilih --" :disabled="!valid"
                                        ></v-autocomplete>
                                    </v-col>

                                    <v-col cols="6" class="pl-2">
                                        <v-text-field
                                            placeholder="Zulfa"
                                            label="Nama Rekening Pengirim"
                                            v-model="paymentBankSender" :disabled="!valid"
                                        ></v-text-field>
                                    </v-col>

                                    <v-col cols="12">
                                        <v-select
                                            :items="accounts"
                                            item-text="account_name"
                                            item-value="account_id"
                                            return-object
                                            v-model="selectedAccount"
                                            label="Rekening Tujuan Transfer"
                                            placeholder="-- Pilih --" :disabled="!valid"
                                        ></v-select>
                                    </v-col>

                                    <v-col cols="12" class="pt-10 pb-5">
                                        <label class="cyan--text">Catatan dan Bukti Transfer</label>
                                        <v-divider></v-divider>
                                    </v-col>

                                    <v-col cols="12">
                                        <v-textarea
                                            label="Catatan"
                                            placeholder="Tuliskan pesan anda disini"
                                            v-model="paymentNote" outlined :disabled="!valid"
                                        ></v-textarea>
                                    </v-col>

                                    <v-col cols="12">
                                        
                                        <v-text-field placeholder=" " label="Bukti Transfer" @click='pickFile' v-model='imageName' prepend-icon='attach_file' clearable :disabled="!valid"></v-text-field>      
                                        <input
                                            type="file"
                                            style="display: none"
                                            ref="image"
                                            accept="image/*"
                                            @change="onFilePicked" :disabled="!valid"
                                        >

                                        <v-img :src="receipt_img" position="center center" class="ma-1" height="auto"></v-img>
                                    </v-col>

                                    <v-col class="text-xs-center" mt-5 cols="12">
                                        <v-btn color="primary" type="submit" :disabled="!valid">Konfirmasi</v-btn>
                                    </v-col>
                                </v-row>
                            </v-form>
                        </v-card-text>
                    </v-card>
                </v-col>
                <v-spacer></v-spacer>
            </v-row>
    </v-container>
</template>

<script>
module.exports = {
    data() {
        return {
            imageName: '',
            imageUrl: '',
            imageFile: '',

            valid: false,
            validForm: true
        }
    },

    components: {
    },

    computed : {
        ...Vuex.mapState('register', []),
        ...Vuex.mapState('payment2', ['accounts', 'banks']),
        ...Vuex.mapState('payment', ['invoiceTotal']),

        __s () { return this.$store.state.payment },

        paymentCode: {
            get () { return this.__s.paymentCode },
            set (v) { this.__c("paymentCode", v) }
        },

        paymentAmount: {
            get () { return this.__s.paymentAmount },
            set (v) { this.__c("paymentAmount", v) }
        },

        paymentDate: {
            get () { return this.__s.paymentDate },
            set (v) { this.__c("paymentDate", v) }
        },

        paymentTime: {
            get () { return this.__s.paymentTime },
            set (v) { this.__c("paymentTime", v) }
        },

        paymentBankSender: {
            get () { return this.__s.paymentBankSender },
            set (v) { this.__c("paymentBankSender", v) }
        },

        paymentBankName: {
            get () { return this.__s.paymentBankName },
            set (v) { this.__c("paymentBankName", v) }
        },

        paymentNote: {
            get () { return this.__s.paymentNote },
            set (v) { this.__c("paymentNote", v) }
        },

        paymentReceipt: {
            get () { return this.__s.paymentReceipt },
            set (v) { this.__c("paymentReceipt", v) }
        },

        selectedBank : {
            get () { return this.__s.selectedBank },
            set (v) { this.__c('selectedBank', v) }
        },

        selectedAccount : {
            get () { return this.__s.selectedAccount },
            set (v) { this.__c('selectedAccount', v) }
        },

        receipt_img : {
            get () {
                if (this.__s.paymentReceipt)
                    return this.__s.paymentReceipt
                return ''
            },
            set (v) { this.__c('paymentReceipt', v) }
        },

        invoiceNumber : {
            get () { return this.__s.invoiceNumber },
            set (v) { this.__c('invoiceNumber', v) } }
    },

    methods : {
        __c (a, b) { this.$store.commit('payment/set_object', [a, b]) },

        pickFile () {
            this.$refs.image.click ()
        },

        onFilePicked (e) {
            const files = e.target.files
            if(files[0] !== undefined) {
                this.imageName = files[0].name
                
                if(this.imageName.lastIndexOf('.') <= 0) {
                    return
                }
                const fr = new FileReader ()
                fr.readAsDataURL(files[0])
                fr.addEventListener('load', () => {
                    this.imageUrl = fr.result
                    this.imageFile = files[0] // this is an image file that can be sent to server...
                    this.getBase64(files[0])
                })
            } else {
                this.imageName = ''
                this.imageFile = ''
                this.imageUrl = ''
            }
        },

        getBase64(file) {
            let vue = this
            let photo_uri = ''
            var reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function () {
            //   console.log(reader.result);
                photo_uri = reader.result
                vue.receipt_img = photo_uri
                reader.onerror = function (error) {
                    console.log('Error: ', error);
                };
            }

            
        },

        searchInvoice() {
            this.$store.dispatch("payment/searchInvoice").then(x => {
                if (x.total == 0) {
                    this.__c("invoiceId", 0), this.__c("invoiceTotal", 0)
                    alert('Nomor Invoice Tidak Diketemukan atau Sudah Terbayar !')

                    this.valid = false
                } else {
                    let y = x.records[0]
                    this.__c("invoiceId", y.L_InvoiceID), this.__c("invoiceTotal", y.L_SoTotal)

                    this.valid = true
                }
            })
        },

        submitForm() {
            // Trigger form validation
            if (this.$refs.form.validate()) {
                // Perform your custom action here
                // console.log('Form Data:', this.formData);
                // alert('Form submitted!');

                this.$store.dispatch("payment/confirm")
                // Reset the form if needed
                // this.$refs.form.reset();
            }
        }
    },

    mounted () {
        with (this.$store) {
            dispatch('payment2/search_bank', {})
            dispatch('payment2/search_account', {})    
        }
    }
}