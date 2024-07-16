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
                                <v-row no-gutters>
                                    <v-col cols="12">
                                        <v-text-field name="name" label="Nama Lengkap" id="name" required
                                            v-model="cust_name"></v-text-field>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-text-field name="address" label="Alamat Lengkap" id="address" required
                                            v-model="cust_address"></v-text-field>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-select :items="sexes" label="Jenis Kelamin" v-model="cust_sex"></v-select>
                                        <!-- <v-radio-group v-model="row" row label="Jenis Kelamin">
                                            <v-radio label="Option 1" value="radio-1"></v-radio>
                                            <v-radio label="Option 2" value="radio-2"></v-radio>
                                        </v-radio-group> -->
                                    </v-col>
                                    <v-col cols="12" class="pt-10 pb-5">
                                        <label class="cyan--text">Data credensial</label>
                                        <v-divider></v-divider>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-text-field name="email" label="Email (digunakan untuk username)" id="email"
                                            type="email" required v-model="cust_email"></v-text-field>
                                    </v-col>
                                    <v-col cols="12">
                                        <v-text-field name="phone" label="Telepon / Handphone" id="phone" prefix="62"
                                            required v-model="cust_phone"></v-text-field>
                                        <!-- <v-text-field name="password" label="Password" id="password" type="password"
                                            required></v-text-field> -->
                                    </v-col>
                                    <!-- <v-col cols="12">
                                        <v-text-field name="confirmPassword" label="Confirm Password"
                                            id="confirmPassword" type="password" required></v-text-field>
                                    </v-col> -->


                                    <v-col cols="12" class="pt-10 pb-5">
                                        <label class="cyan--text">Data Kursus</label>
                                        <v-divider></v-divider>
                                    </v-col>
                                    <v-col cols="8" class="pr-2">
                                        <v-select :items="items" label="Pilih kursus" chips item-value="M_ItemID"
                                            item-text="M_ItemName" v-model="selected_item"
                                            return-object></v-select>
                                    </v-col>
                                    <v-col cols="4">
                                        <v-select :items="levels" label="Pilih kelas" chips
                                            item-value="M_CustomerLevelID" item-text="M_CustomerLevelName"
                                            v-model="selectedLevel"></v-select>
                                    </v-col>

                                    <v-col class="text-xs-center d-flex" mt-5 cols="12">
                                        <v-spacer></v-spacer>
                                        <v-btn color="primary" type="submit">Selanjutnya</v-btn>
                                    </v-col>
                                </v-row>
                            </v-form>
                        </v-card-text>
                    </v-card>
                </v-col>
                <v-spacer></v-spacer>
            </v-row>
        </v-col>


    </v-row>
</template>

<style scoped></style>

<script>
module.exports = {
    data() {
        return {
            carouselItems: [
                {
                    src: 'https://cdn.vuetifyjs.com/images/carousel/squirrel.jpg',
                }
            ],

            featuredItems: [
                {
                    color: '#1F7087',
                    src: 'https://cdn.vuetifyjs.com/images/cards/foster.jpg',
                    title: 'Supermodel',
                    artist: 'Foster the People',
                },
                {
                    color: '#952175',
                    src: 'https://cdn.vuetifyjs.com/images/cards/halcyon.png',
                    title: 'Halcyon Days',
                    artist: 'Ellie Goulding',
                },
            ],

            loading: false,
            selection: 1,
            valid: false
        }
    },

    computed: {
        ...Vuex.mapState('register', ['sexes', 'items', 'levels', 'cust_id']),
        ...Vuex.mapState({ __s: s => s.register }),

        cust_name: {
            get() { return this.__s.cust_name },
            set(v) { this.setObject('cust_name', v) }
        },

        cust_address: {
            get() { return this.__s.cust_address },
            set(v) { this.setObject('cust_address', v) }
        },

        cust_email: {
            get() { return this.__s.cust_email },
            set(v) { this.setObject('cust_email', v) }
        },

        cust_phone: {
            get() { return this.__s.cust_phone },
            set(v) { this.setObject('cust_phone', v) }
        },

        cust_sex: {
            get() { return this.__s.cust_sex },
            set(v) { this.setObject('cust_sex', v) }
        },

        order_note: {
            get() { return this.__s.order_note },
            set(v) { this.setObject('order_note', v) }
        },

        selected_payment: {
            get() { return this.__s.selected_payment },
            set(v) { this.setObject('selected_payment', v) }
        },

        selected_item: {
            get() { return this.__s.selected_item },
            set(v) { this.setObject('selected_item', v) }
        },

        selectedLevel: {
            get() { return this.__s.selectedLevel },
            set(v) { this.setObject('selectedLevel', v) }
        },

        step: {
            get () { return this.$store.state.register.step },
            set (v) { this.$store.commit('register/set_object', ['step', v]) } }
    },

    methods: {
        setObject(a, b) {
            this.$store.commit('register/set_object', [a, b])
        },

        reserve() {
            this.loading = true

            setTimeout(() => (this.loading = false), 2000)
        },

        submitForm() {
            // Trigger form validation
            if (this.$refs.form.validate()) {
                // Perform your custom action here
                // console.log('Form Data:', this.formData);
                // alert('Form submitted!');

                this.step++
                // Reset the form if needed
                // this.$refs.form.reset();
            }
        }
    },

    mounted() {
        this.$store.dispatch("register/search_item")
        this.$store.dispatch("register/search_level")
    }
}
</script>