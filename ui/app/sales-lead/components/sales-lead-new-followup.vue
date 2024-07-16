<template>
    <div>
        <v-tabs v-model="fuTab" color="#25D366" dark slider-color="yellow">
            <v-tab v-for="(f, n) in followUps" :key="n" ripple>
                FOLLOW UP {{ n + 1 }}
                <v-btn small class="btn-icon mr-0 red elevation-0 lighten-3" @click="fuDel(n)">X</v-btn>
            </v-tab>
            <v-spacer></v-spacer>
            <div class="d-flex align-center">
                <v-btn color="white" small class="" depressed flat @click="fuAdd()" :disabled="!edit||leadClose=='Y'||!fuAddEnabled">+ FOLLOW UP</v-btn>
            </div>

            <v-tab-item v-for="(f, n) in followUps" :key="n" ripple>
                <v-card>
                    <v-card-text>
                        <v-layout row wrap>
                            <v-flex xs3>
                                <v-text-field @click="openDate('x')" :value="f.fu_date" readonly label="Tanggal"></v-text-field>
                            </v-flex>
                            <v-flex xs9 class="text-xs-right">
                                <v-chip color="green" text-color="white">
                                    <v-avatar class="green darken-4"><img src="../assets/img/logo-whatsapp-64x64.png" /></v-avatar>
                                    {{ !!selectedCustomer ? selectedCustomer.M_CustomerPhone : lead_phone }}
                                </v-chip>
                            </v-flex>
                            <v-flex xs12>
                                <v-textarea label="Catatan" rows="2" outline :value="f.fu_note" @change="setValue('note', $event)"></v-textarea>
                            </v-flex>
                            <v-flex xs12>
                                <v-btn-toggle :value="f.fu_close" mandatory class="mb-3 pa-1" style="width:100%" @change="setValue('close', $event)">
                                        <v-btn flat color="success" value="Y" large>
                                            Closing
                                        </v-btn>
                                        <v-btn flat color="red" value="N" large>
                                            Belum Closing
                                        </v-btn>
                                        <v-select :items="leadPrecloses" item-value="attr_id" item-text="attr_name"
                                            chips solo dense label="Jika belum closing, apa alasannya"
                                            hint="Jika CLOSING, silahkan dikosongi kolom ini" clearable
                                            :value="f.fu_preclose" :disabled="f.fu_close == 'Y'" prefix="Alasan "
                                            hide-details class="elevation-0 ml-2" @change="setValue('preclose', $event)"></v-select>
                                    </v-btn-toggle>
                            </v-flex>

                            <v-flex xs12>
                                <v-card>
                                    <v-img
                                    :src="'../assets/img/wa-bg.jpeg'"
                                    height="200px"
                                    >
                                        <v-container
                                            fill-height
                                            fluid
                                            pa-2
                                        >
                                            <v-layout fill-height>
                                                <v-flex xs6 align-end flexbox>
                                                    <span class="headline white--text" v-text="'Kirim WA'"></span>
                                                </v-flex>
                                                <v-flex xs6 align-end flexbox>
                                                    <v-textarea class="white" label="Message" style="height:100%" flat solo v-model="waText"></v-textarea>
                                                </v-flex>
                                            </v-layout>
                                        </v-container>
                                    </v-img>

                                    <v-card-actions>
                                        <h5>NO : <span class="cyan--text">{{ !!selectedCustomer ? selectedCustomer.M_CustomerPhone : lead_phone }}</span></h5>
                                        <v-spacer></v-spacer>
                                        <v-chip color="green" text-color="white" class="v-chip__wa-button" @click="waSend">
                                            <v-btn icon class="mr-2">
                                                <v-img src="../assets/img/logo-whatsapp-64x64.png" contain></v-img>
                                            </v-btn>
                                            Kirim
                                        </v-chip>
                                    </v-card-actions>
                                </v-card>
                            </v-flex>
                        </v-layout>
                    </v-card-text>
                </v-card>
            </v-tab-item>
        </v-tabs>

        <datex @change="setDate"></datex>
    </div>
</template>

<style scoped>
.v-chip.v-chip__wa-button .v-chip__content {
    padding-left: 0px;
}
</style>

<script>
module.exports = {
    components: {
        datex: httpVueLoader("../../common/components/common-date-dialog-x.vue")
    },

    data() {
        return {
            whichDate: '',
            waText: ''
        }
    },

    computed: {

        ...Vuex.mapState('saleslead_new',
            ['followUps', 'defaultFollowUp', 'leadPrecloses', 'leadClose', 'lead_phone', 'selectedCustomer', 'edit']),

        __s() { return this.$store.state.saleslead_new },

        fuTab: {
            get() { return this.__s.fuTab },
            set(v) { this.__c('fuTab', v) }
        },

        fuAddEnabled () {
            let l = this.followUps.length
            if (l > 0 && this.followUps[l-1].fu_id == 0) return false
            return true
        }
    },

    methods: {
        __c(a, b) { this.$store.commit('saleslead_new/set_object', [a, b]) },

        fuAdd() {
            let x = structuredClone(this.followUps), y = structuredClone(this.defaultFollowUp)
            x.push(y)
            this.__c("followUps", x)
        },

        fuDel(x) {
            let y = structuredClone(this.followUps)
            y.splice(x, 1)
            this.__c("followUps", y)
        },

        openDate(x) {
            // if (!this.enabled) return false
            if (!!x) this.whichDate = x

            let __c = function (x, a, b) { x.$store.commit("xdate/SET_OBJECT", [a, b]) }
            __c(this, "dateXDialog", true)
        },

        setDate(x) {
            let fus = structuredClone(this.followUps)
            fus[this.fuTab].fu_date = x

            this.__c("followUps", fus)
            // if (this.whichDate == 'd')
            //     this.detailDate = x
            // if (!!this.whichDate.match(/(r)[0-9]+/)) {
            //     let i = this.whichDate.replace(/(r)/, "")
            //     this.setRevisionData(i, "rev_date", x)
            // }
        },

        setValue(x, y) {
            let fus = structuredClone(this.followUps)
            fus[this.fuTab]['fu_'+x] = y
            if (x=='close'&&y=='Y') fus[this.fuTab].fu_preclose = null

            this.__c("followUps", fus)
        },

        waSend () {
            const no = window.phoneFormat(!!this.selectedCustomer ? this.selectedCustomer.M_CustomerPhone : this.lead_phone)

            this.__c("leadWaText", this.waText)
            this.$store.dispatch("saleslead_new/waSend").then(x => {
                if (!!this.waText) {
                    const encodedMessage = encodeURIComponent(this.waText);
                    window.open('https://wa.me/'+no+'?text='+encodedMessage, '_blank');
                }
                else
                    window.open('https://wa.me/'+no, '_blank');
            })
            this.__c("leadWaText", "")
        }
    }
}