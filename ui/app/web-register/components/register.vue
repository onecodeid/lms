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

                                    <v-col cols="12" v-if="!!itemId" class="mb-3">
                                        <v-card :color="'grey'" dark v-if="!!selected_item">
                                            <div class="d-flex flex-no-wrap justify-space-between">
                                                <div>
                                                    <v-card-title class="text-h5"
                                                        v-text="selected_item.M_ItemName"></v-card-title>

                                                        <v-card-text>
                                                            <div>{{ selected_item.M_ItemDesc }}</div>
                                                        </v-card-text>
                                                    <!-- <v-card-subtitle v-text="item.artist"></v-card-subtitle> -->

                                                    <v-card-actions>
                                                        <!-- <v-btn v-if="item.artist === 'Ellie Goulding'" class="ml-2 mt-3" fab icon
                                        height="40px" right width="40px">
                                        <v-icon>mdi-play</v-icon>
                                    </v-btn> -->

                                                        <!-- <v-btn class="ml-2 mt-5" outlined rounded small>
                                        START RADIO
                                    </v-btn> -->
                                                    </v-card-actions>
                                                </div>

                                                <v-avatar class="ma-3" size="125" tile>
                                                    <v-img :src="selected_item.img_url"></v-img>
                                                </v-avatar>
                                            </div>
                                        </v-card>
                                    </v-col>

                                    <v-col cols="8" class="pr-2" v-else>
                                        <v-select :items="items" label="Pilih kursus" chips item-value="M_ItemID"
                                            item-text="M_ItemName" v-model="selected_item" return-object></v-select>
                                    </v-col>
                                    <v-col cols="4">
                                        <v-select :items="levels" label="Pilih kelas" chips
                                            item-value="M_CustomerLevelID" item-text="M_CustomerLevelName"
                                            v-model="selectedLevel"></v-select>
                                    </v-col>

                                    <v-col cols="12" v-if="!!selectedLevel&&selectedLevel==5">
                                        <v-select :items="schedules" label="Pilih Jadwal" chips
                                            item-value="schedule_id" item-text="day_name"
                                            v-model="selected_schedule" solo>
                                            <template slot="item" slot-scope="data">
                                                {{ data.item.schedule_days.map(sch=>sch.day_name).join(", ") }}
                                                <!-- {{ data.item.day_name }} <span class="font-weight-light mx-2">jam</span> {{ data.item.schedule_time }} -->
                                            </template>
                                            <template slot="selection" slot-scope="data">
                                                {{ data.item.schedule_days.map(sch=>sch.day_name).join(", ") }}
                                                <!-- {{ data.item.day_name }} <span class="font-weight-light mx-2">jam</span> {{ data.item.schedule_time }} -->
                                            </template>
                                        </v-select>
                                    </v-col>

                                    <v-col cols="12">
                                        <v-row no-gutters>
                                            <v-col cols="4" v-if="!!selectedLevel&&selectedLevel==1">
                                                <v-select :items="days" label="Pilih Hari" chips
                                                    item-value="day_id" item-text="day_name"
                                                    v-model="selected_day" solo>
                                                </v-select>
                                            </v-col>

                                            <v-col cols="4" class="pl-2" v-if="!!selectedLevel&&selectedLevel==1">
                                                <v-text-field
                                                    solo
                                                    hide-details
                                                    class="input-dense"
                                                    v-model="schedule_time"
                                                    reverse
                                                    suffix="Jam"
                                                ></v-text-field>
                                            </v-col>
                                        </v-row>
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
        ...Vuex.mapState('register', ['sexes', 'items', 'levels', 'cust_id', 'schedules', 'days']),
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
            set(v) { 
                this.setObject('selected_item', v)
                this.$store.dispatch("register/search_schedule")
            }
        },

        selectedLevel: {
            get() { return this.__s.selectedLevel },
            set(v) { this.setObject('selectedLevel', v) }
        },

        step: {
            get() { return this.$store.state.register.step },
            set(v) { this.$store.commit('register/set_object', ['step', v]) }
        },

        itemId () {
            // Membuat instance dari URLSearchParams
            const params = new URLSearchParams(window.location.search);
            const itemId = params.get('item_id');

            return itemId
        },

        selected_schedule: {
            get() { return this.__s.selected_schedule },
            set(v) { this.setObject('selected_schedule', v) }
        },

        selected_day: {
            get() { return this.__s.selected_day },
            set(v) { this.setObject('selected_day', v) }
        },

        schedule_time: {
            get() { return this.__s.schedule_time },
            set(v) { this.setObject('schedule_time', v) }
        }
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
        // Membuat instance dari URLSearchParams
        const params = new URLSearchParams(window.location.search);

        // Mendapatkan nilai dari parameter 'name'
        const itemId = params.get('item_id'); // 'John'
        // console.log(itemId);
        this.$store.dispatch("register/search_item").then(d => {
            if (!!itemId)
                for (let itm of d.records)
                    if (parseInt(itm.M_ItemID) == parseInt(itemId)) this.selected_item = itm
        })
        this.$store.dispatch("register/search_level")
        this.$store.dispatch("register/search_day")
    }
}
</script>