// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_item.js"
import { one_token } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        edit: false,
        search_status: 0,
        search_error_message: '',
        search: '',
        
        selected_item: {},
        dialog_new: false,

        item_name: "",
        item_code: "",
        item_address: "",
        item_weight: 0,
        item_img: '',
        item_min: 0,
        item_hpp: 0,
        item_publish: 'N',

        units : [],
        selected_unit: {},
        total_unit: 0,

        categories : [],
        selected_category: {},
        total_category: 0,

        levels: [],
        selected_price: {},
        total_level: 0,

        schedules: [],
        selectedSchedule: null,
        scheduleDefault: { id:0, day:0, time:'00:00', capacity:0 },
        days: [],

        v2Schedules: [],
        v2ScheduleDefault: { id:0, days:null, capacity:0 },

        sdate: '',
        edate: '',

        fees: []
    },
    mutations: {
        set_common (state, v) {
            let name = v[0]
            let val = v[1]
            if (typeof(val) == "string")
                eval(`state.${name} = "${val}"`)
            else
                eval(`state.${name} = ${val}`)
        },

        set_object(state, v) {
            let name = v[0]
            let val = v[1]
            state[name] = val
        },

        update_search_error_message(state, msg) {
            state.search_error_message = msg
        },

        update_search(state, search) {
            state.search = search
        },

        set_selected_item(state, val) {
            state.selected_item = val
        },

        update_search_status(state, val) {
            state.search_status = val
        },

        set_dialog_new (state, v) {
            state.dialog_new = v
        },

        set_units (state, v) {
            state.units = v
        },

        set_selected_unit (state, v) {
            state.selected_unit = v
        },

        set_categories (state, v) {
            state.categories = v
        },

        set_selected_category (state, v) {
            state.selected_category = v
        },

        set_levels (state, v) {
            state.levels = v
        },

        set_selected_level (state, v) {
            state.selected_level = v
        },

        set_fees (state, v) {
            state.fees = v
        }
    },
    actions: {
        async save(context) {
            
            try {
                let prices = []
                let fees = []
                let level
                for (level of context.state.levels)
                    prices.push({price_level_id:level.M_CustomerLevelID,
                                price_amount:level.M_PriceAmount,
                                price_disc:level.M_PriceDisc,
                                price_disc2:level.M_PriceDisc2,
                                price_discrp:level.M_PriceDiscRp,
                                price_sale:level.M_PriceSale,
                                price_sale_sdate:context.state.sdate,
                                price_sale_edate:context.state.edate})

                for (let fee of context.state.fees)
                    fees.push({fee_level_id:fee.M_CustomerLevelID,
                                fee_amount:fee.M_FeeAmount,
                                point_amount:fee.M_FeePointAmount,
                                reward_amount:fee.M_FeeRewardAmount})

                let prm = {token : one_token(), 
                            item_name: context.state.item_name,
                            item_code: context.state.item_code,
                            item_weight: context.state.item_weight,
                            item_unit_id: context.state.selected_unit.M_UnitID,
                            item_category_id: context.state.selected_category.M_CategoryID,
                            item_img: context.state.item_img,
                            item_min: context.state.item_min,
                            item_hpp: context.state.item_hpp,
                            item_publish: context.state.item_publish,
                            prices: JSON.stringify(prices),
                            fees: JSON.stringify(fees),
                            schedules: JSON.stringify(context.state.schedules),
                            v2schedules: JSON.stringify(context.state.v2Schedules)
                        }

                if (context.state.edit)
                        prm.item_id = context.rootState.item.selected_item.M_ItemID

                let resp = await api.save(prm)
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("set_common", ["search_status", 2])
                    context.commit("update_search_error_message", "")
                    // let data = {
                    //     records: resp.data.records,
                    //     total: resp.data.total
                    // }
                    
                    context.commit("set_common", ["dialog_new", false])
                    context.dispatch("item/search", {}, {root:true})
                    // context.commit("set_cities", data.records)
                    // context.commit("update_common", data.total)
                }
            } catch (e) {
                context.commit("set_common", ["search_city_status", 3])
                context.commit("update_search_error_message", e.message)
                console.log(e)
            }
        },

        async search_unit(context, prm) {
            context.commit("update_search_status", 1)
            try {
                prm.token = one_token()
                let resp = await api.search_unit(prm)
                
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")
                    let data = {
                        records: resp.data.records,
                        total: resp.data.total
                    }

                    context.commit("set_units", data.records)
                    context.commit("set_common", ["total_unit", data.total])
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                console.log(e)
            }
        },

        async search_category(context, prm) {
            context.commit("update_search_status", 1)
            try {
                prm.token = one_token()
                let resp = await api.search_category(prm)
                
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")
                    let data = {
                        records: resp.data.records,
                        total: resp.data.total
                    }

                    context.commit("set_categories", data.records)
                    context.commit("set_common", ["total_category", data.total])
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                console.log(e)
            }
        },

        async search_level_price(context, prm) {
            context.commit("update_search_status", 1)
            try {
                prm.token = one_token()
                let resp = await api.search_level_price(prm)
                
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")
                    let data = {
                        records: resp.data.records,
                        total: resp.data.total
                    }

                    context.commit("set_levels", data.records)
                    context.commit("set_common", ["total_level", data.total])
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                console.log(e)
            }
        },

        async search_day({state, commit, dispatch}) {
            let prm = {
                token : one_token(),
                search : ""
            }

            dispatch("system/postme", {
                url: "master/day/search",
                prm: prm,
                callback: function(d) {
                    commit("set_object", ['days', d.records])
                    console.log(d)
                },
                failback: function(e) {
                    // context.commit('set_common', ['loading_city', false])
                }
            }, { root: true })
        }
    }
}