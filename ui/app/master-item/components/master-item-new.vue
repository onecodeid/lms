<template>
    <v-dialog
        v-model="dialog"
        scrollable
        :overlay="false"
        max-width="1200px"
        transition="dialog-transition"
    >
        <v-card>
            <v-card-title primary-title class="cyan white--text pt-3">
                <h3>ITEM BARU</h3>
            </v-card-title>
            <v-card-text>
                <v-layout row wrap>
                    <v-flex xs5 pr-5>
                        <v-layout row wrap>
                            <v-flex xs12 :class="`mt-`+v_space">
                                <v-text-field
                                    label="Nama Barang"
                                    v-model="item_name"
                                ></v-text-field>
                            </v-flex>

                            <!-- <v-flex xs6 pr-3>
                                
                            </v-flex> -->

                            

                            

                            <v-flex xs6 :class="`mt-`+v_space+` pr-5`">
                                <v-text-field
                                    label="Kode Barang (SKU)"
                                    v-model="item_code"
                                ></v-text-field>

                                <v-select
                                    :items="categories"
                                    v-model="selected_category"
                                    return-object
                                    label="Kategori"
                                    item-key="M_CategoryID"
                                    item-text="M_CategoryName"
                                ></v-select>

                                <v-text-field
                                    label="Lama Kursus"
                                    v-model="item_weight"
                                    suffix="pertemuan"
                                ></v-text-field>
                                
                            </v-flex>

                            
                            <v-flex xs6 align-center
          justify-center>
                                <v-img :src="item_img" position="center center" class="ma-3">
                                </v-img>
                                <v-text-field label="Select Image" @click='pickFile' v-model='imageName' prepend-icon='attach_file'></v-text-field>
                                
                                <input
                                    type="file"
                                    style="display: none"
                                    ref="image"
                                    accept="image/*"
                                    @change="onFilePicked"
                                >

                                <v-checkbox
                                    label="Publish ?"
                                    hide-details
                                    v-model="item_publish"
                                    true-value="Y"
                                    false-value="N"
                                    v-show="!edit"
                                ></v-checkbox>
                            </v-flex>
                            
                            <v-flex xs6  :class="[`mt-`+v_space, 'pl-4']">
                                
                            </v-flex>
                        </v-layout>
                    </v-flex>

                    <v-flex xs7>
                        <v-layout row wrap>
                            <v-data-table 
                                :headers="headers"
                                :items="levels"
                                :loading="false"
                                hide-actions
                                class="elevation-1">
                                <template slot="items" slot-scope="props">
                                    <td class="text-xs-left pa-2"
                                        :class="{'zalfa-bg-purple lighten-3 white--text':(props.item.M_CustomerLevelIsNew=='Y')}">{{ props.item.M_CustomerLevelName }}</td>
                                    <td class="text-xs-left pa-2">
                                        <v-text-field
                                            solo
                                            :value="props.item.M_PriceAmount"
                                            hide-details
                                            reverse
                                            class="input-dense"
                                            @input="change_price('Amount', props.index, $event)"
                                        ></v-text-field>
                                    </td>

                                    <td class="text-xs-left pa-2">
                                        <v-text-field
                                            solo
                                            :value="props.item.M_PriceDiscRp"
                                            hide-details
                                            reverse
                                            class="input-dense"
                                            @input="change_price('DiscRp', props.index, $event)"
                                        ></v-text-field>
                                    </td>

                                    <td class="text-xs-left pa-2">
                                        <v-text-field
                                            solo
                                            :value="props.item.M_PriceTotal"
                                            hide-details
                                            reverse
                                            class="input-dense"
                                            disabled
                                        ></v-text-field>
                                    </td>

                                    <!-- <td class="text-xs-left pa-2 zalfa-bg-cyan lighten-5">
                                        <v-text-field
                                            solo
                                            :value="fees[props.index] ? fees[props.index].M_FeeAmount : 0"
                                            hide-details
                                            reverse
                                            class="input-dense"
                                            @input="change_price('fee', props.index, $event)"
                                        ></v-text-field>
                                    </td>

                                    <td class="text-xs-left pa-2 zalfa-bg-cyan lighten-5">
                                        <v-text-field
                                            solo
                                            :value="fees[props.index] ? fees[props.index].M_FeePointAmount : 0"
                                            hide-details
                                            reverse
                                            class="input-dense"
                                            @input="change_price('point', props.index, $event)"
                                        ></v-text-field>
                                    </td>

                                    <td class="text-xs-left pa-2 zalfa-bg-cyan lighten-5">
                                        <v-text-field
                                            solo
                                            :value="fees[props.index] ? fees[props.index].M_FeeRewardAmount : 0"
                                            hide-details
                                            reverse
                                            class="input-dense"
                                            @input="change_price('reward', props.index, $event)"
                                        ></v-text-field>
                                    </td> -->
                                </template>
                            </v-data-table>
                            <v-divider class="mb-4 mt-2"></v-divider>                            
                        </v-layout>

                        <v-layout row wrap>
                            <v-flex xs12 class="d-flex">
                                <h3 class="title mt-4">Jadwal</h3><v-spacer></v-spacer>
                                <v-btn @click="scheduleAdd">+</v-btn>
                            </v-flex>
                            <v-flex xs12>
                                <v-layout row wrap v-for="(sch, n) in schedules" :key="n" class="pb-2">
                                    <v-flex xs4>
                                        <v-select
                                            solo
                                            hide-details
                                            class="input-dense"
                                            :value="sch.day"
                                            :items="days" item-value="day_id" item-text="day_name"
                                            @change="changeSchValue(n, 'day', $event)"
                                        ></v-select>
                                    </v-flex>
                                    <v-flex xs3 class="pl-2">
                                        <v-text-field
                                            solo
                                            hide-details
                                            class="input-dense"
                                            :value="sch.time"
                                            reverse
                                            suffix="Jam"
                                            @change="changeSchValue(n, 'time', $event)"
                                        ></v-text-field>
                                    </v-flex>
                                    <v-flex xs3 class="pl-2">
                                        <v-text-field
                                            solo
                                            hide-details
                                            class="input-dense"
                                            :value="sch.capacity"
                                            reverse
                                            suffix="Kapasitas"
                                            @change="changeSchValue(n, 'capacity', $event)"
                                        ></v-text-field>
                                    </v-flex>
                                </v-layout>
                            </v-flex>
                        </v-layout>
                        <!-- <v-layout row wrap>
                            <v-flex xs4>
                                <h3 class="title mt-4">Harga Promo</h3>
                            </v-flex>
                            <v-flex xs2 mb-2>
                                <common-datepicker
                                    label="Tanggal berlaku"
                                    :date="sdate"
                                    data="0"
                                    @change="change_sdate"
                                    classs="mt-0 input-dense"
                                    hints=""
                                    :details="false"
                                    :solo="false"
                                    v-if="dialog"
                                ></common-datepicker>
                            </v-flex>

                            <v-flex xs2 mb-2>
                                <common-datepicker
                                    label="Sampai Tanggal"
                                    :date="edate"
                                    data="0"
                                    @change="change_edate"
                                    classs="mt-0 ml-1 input-dense"
                                    hints=""
                                    :details="false"
                                    :solo="false"
                                    v-if="dialog"
                                ></common-datepicker>
                            </v-flex>
                            <v-flex xs4>
                                
                            </v-flex>
                            
                            <v-flex xs12>
                                <div class="elevation-1">
                                    <v-layout row wrap>
                                        <v-flex xs3 v-for="(level, n) of levels" v-bind:key="n" class="pa-1">
                                            <v-layout column>
                                                <v-flex pa-1 class="zalfa-bg-purple lighten-3 white--text">
                                                    {{level.M_CustomerLevelName.toUpperCase()}}
                                                </v-flex>
                                                <v-flex>
                                                    <v-text-field
                                                        solo
                                                        hide-details
                                                        reverse
                                                        class="input-dense"
                                                        :value="prices[level.M_CustomerLevelCode]?prices[level.M_CustomerLevelCode].sale:0"
                                                        @change="change_price('Sale', n, $event)"
                                                    ></v-text-field>
                                                </v-flex>
                                            </v-layout>
                                        </v-flex>
                                    </v-layout>
                                </div>  
                            </v-flex>
                        </v-layout> -->

                    </v-flex>
                </v-layout>
            </v-card-text>

            <v-card-actions>
                <v-btn color="primary" flat @click="dialog=!dialog">Batal</v-btn>
                <v-spacer></v-spacer>
                <v-btn color="success" @click="publish('Y')" v-show="!!edit && item_publish=='N'">Publish</v-btn>
                <v-btn color="red" dark @click="publish('N')" v-show="!!edit && item_publish=='Y'">UnPublish</v-btn>
                <v-btn color="primary" @click="save">Simpan</v-btn>                
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<style>
.input-dense .v-input__control {
    min-height: 36px !important;
}
</style>
<script>
module.exports = {
    components : {
        'common-datepicker' : httpVueLoader('../../common/components/common-datepicker.vue')
    },

    data () {
        return {
            headers: [
                {
                    text: "LEVEL",
                    align: "center",
                    sortable: false,
                    width: "14%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "HARGA",
                    align: "center",
                    sortable: false,
                    width: "14%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "DISKON",
                    align: "center",
                    sortable: false,
                    width: "14%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "TOTAL",
                    align: "center",
                    sortable: false,
                    width: "14%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                // {
                //     text: "KOMISI",
                //     align: "center",
                //     sortable: false,
                //     width: "14%",
                //     class: "pa-2 zalfa-bg-cyan lighten-3 white--text"
                // },
                // {
                //     text: "POIN",
                //     align: "center",
                //     sortable: false,
                //     width: "14%",
                //     class: "pa-2 zalfa-bg-cyan lighten-3 white--text"
                // },
                // {
                //     text: "REWARD",
                //     align: "center",
                //     sortable: false,
                //     width: "14%",
                //     class: "pa-2 zalfa-bg-cyan lighten-3 white--text"
                // }
            ],

            imageName: '',
            imageUrl: '',
            imageFile: ''
        }
    },

    computed : {
        ...Vuex.mapState({
            edit: s => s.item_new.edit,
            schedules: s => s.item_new.schedules,
            scheduleDefault: s => s.item_new.scheduleDefault,
            days: s => s.item_new.days
        }),

        dialog : {
            get () { return this.$store.state.item_new.dialog_new },
            set (v) { this.$store.commit('item_new/set_common', ['dialog_new', v]) }
        },

        item_name : {
            get () { return this.$store.state.item_new.item_name },
            set (v) { this.$store.commit('item_new/set_common', ['item_name', v]) }
        },

        item_code : {
            get () { return this.$store.state.item_new.item_code },
            set (v) { this.$store.commit('item_new/set_common', ['item_code', v]) }
        },

        item_weight : {
            get () { return this.$store.state.item_new.item_weight },
            set (v) { this.$store.commit('item_new/set_common', ['item_weight', v]) }
        },

        item_img : {
            get () {
                if (this.$store.state.item_new.item_img)
                    return this.$store.state.item_new.item_img
                return ''
            },
            set (v) { this.$store.commit('item_new/set_common', ['item_img', v]) }
        },

        item_min : {
            get () { return this.$store.state.item_new.item_min },
            set (v) { this.$store.commit('item_new/set_common', ['item_min', v]) }
        },

        item_hpp : {
            get () { return this.$store.state.item_new.item_hpp },
            set (v) { this.$store.commit('item_new/set_common', ['item_hpp', v]) }
        },

        item_publish : {
            get () { return this.$store.state.item_new.item_publish },
            set (v) { this.$store.commit('item_new/set_common', ['item_publish', v]) }
        },

        units () {
            return this.$store.state.item_new.units
        },

        selected_unit : {
            get () { return this.$store.state.item_new.selected_unit },
            set (v) { this.$store.commit('item_new/set_selected_unit', v) }
        },

        categories () {
            return this.$store.state.item_new.categories
        },

        selected_category : {
            get () { return this.$store.state.item_new.selected_category },
            set (v) { this.$store.commit('item_new/set_selected_category', v) }
        },

        v_space () {
            return 0
        },

        levels () {
            return this.$store.state.item_new.levels
        },

        fees () {
            return this.$store.state.item_new.fees
        },

        edate : {
            get () { return this.$store.state.item_new.edate },
            set (v) { this.$store.commit('item_new/set_common', ['edate', v]) }
        },

        sdate : {
            get () { return this.$store.state.item_new.sdate },
            set (v) { this.$store.commit('item_new/set_common', ['sdate', v]) }
        },

        prices () {
            let p = {}
            if (!this.$store.state.item.selected_item.prices)
                return p
            for (let price of this.$store.state.item.selected_item.prices) {
                p[price.M_CustomerLevelCode] = {sale:price.M_PriceSale,sale_sdate:price.M_PriceSaleStartDate,sale_edate:price.M_PriceSaleEndDate}
            }
            return p
        }
    },

    methods : {
        save () {
            this.$store.dispatch('item_new/save')
        },

        change_price(type, i, v) {
            if (type == "fee") {
                let x = this.fees
                x[i][`M_FeeAmount`] = v
                this.$store.commit('item_new/set_fees', x)
            } else if (type == "point") {
                let x = this.fees
                x[i][`M_FeePointAmount`] = v
                this.$store.commit('item_new/set_fees', x)
            } else if (type == "reward") {
                let x = this.fees
                x[i][`M_FeeRewardAmount`] = v
                this.$store.commit('item_new/set_fees', x)
            } else if (type == "sale") {
                let x = this.levels
                x[i][`M_Price${type}`] = v
                this.$store.commit('item_new/set_levels', x)
            } else {
                let x = this.levels
                x[i][`M_Price${type}`] = v
                x[i][`M_PriceTotal`] = parseFloat(x[i]['M_PriceAmount']) - parseFloat(x[i]['M_PriceDiscRp'])

                this.$store.commit('item_new/set_levels', x)
            }
        },

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
                vue.item_img = photo_uri
                reader.onerror = function (error) {
                    console.log('Error: ', error);
                };
            }

            
        },

        change_edate(x) {
            this.edate = x.new_date
        },

        change_sdate(x) {
            this.sdate = x.new_date
        },

        publish(x) {
            this.$store.commit('item_new/set_common', ['item_publish', x])
            this.save()
        },

        scheduleAdd() {
            let schs = structuredClone(this.schedules)
            schs.push(structuredClone(this.scheduleDefault))

            this.$store.commit('item_new/set_object', ['schedules', schs])
        },

        changeSchValue(x, y, z) {
            let schs = structuredClone(this.schedules)
            schs[x][y] = z

            this.$store.commit('item_new/set_object', ['schedules', schs])
        }
    },

    mounted () {
        this.$store.dispatch('item_new/search_unit', [])
        this.$store.dispatch('item_new/search_category', [])
        this.$store.dispatch('item_new/search_level_price', [])
        this.$store.dispatch('item_new/search_day')
    },

    watch : {
        
    }
}
</script>