// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_reward.js"
import { one_token } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        search_status: 0,
        search_error_message: '',
        search: '',
        
        rewards: [],
        total_reward: 0,
        total_reward_page: 0,
        selected_reward: {},

        customer_single: null,
        customers: [],
        total_customer: 0,
        total_customer_page: 0,
        selected_customer: null,
        current_customer_page: 1,
        search_customer: '',

        current_page: 1,
        snackbar: false,
        redeem_text: '',

        redeem: false
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

        update_search_error_message(state, msg) {
            state.search_error_message = msg
        },

        update_search(state, search) {
            state.search = search
        },

        update_rewards(state, data) {
            state.rewards = data
        },

        set_selected_reward(state, val) {
            state.selected_reward = val
        },

        update_search_status(state, val) {
            state.search_status = val
        },

        update_total_reward(state, val) {
            state.total_reward = val
        },

        update_total_reward_page(state, val) {
            state.total_reward_page = val
        },

        update_current_page(state, val) {
            state.current_page = val
        },

        set_customers(state, data) {
            state.customers = data
        },

        set_selected_customer(state, data) {
            state.selected_customer = data
        }
    },
    actions: {
        async search(context, prm) {
            context.commit("update_search_status", 1)
            try {
                prm.token = one_token()
                prm.search = context.state.search
                prm.page = context.state.current_page
                let resp = await api.search(prm)
                
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")
                    let data = {
                        records: resp.data.records,
                        total: resp.data.total,
                        total_page: resp.data.total_page
                    }

                    context.commit("update_rewards", data.records)
                    context.commit("update_total_reward", data.total)
                    context.commit("update_total_reward_page", data.total_page)
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                console.log(e)
            }
        },

        async del(context, prm) {
            context.commit("update_search_status", 1)
            try {
                prm.token = one_token()
                prm.id = context.state.selected_reward.M_RewardID

                let resp = await api.del(prm)
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")
                    
                    context.dispatch('search', {})
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                console.log(e)
            }
        },

        async search_customer(context, prm) {
            context.commit("update_search_status", 1)
            try {
                prm.token = one_token()
                prm.page = context.state.current_customer_page
                prm.search = context.state.search_customer
                prm.city = 0
                prm.province= 0
                prm.level = 0
                let resp = await api.search_customer(prm)
                console.log(resp)
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")
                    let data = {
                        records: resp.data.records,
                        total: resp.data.total,
                        total_page: resp.data.total_page
                    }

                    context.commit("set_customers", data.records)
                    context.commit("set_common", ['total_customer', data.total])
                    context.commit("set_common", ['total_customer_page', data.total_page])
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                console.log(e)
            }
        },

        async redeem_reward(context, prm) {
            context.commit("update_search_status", 1)
            context.commit('set_common', ['redeem', true])
            try {
                prm.token = one_token()
                prm.customer_id = context.state.selected_customer.M_CustomerID
                prm.reward_id = context.state.selected_reward.M_RewardID
                prm.note = ""
                let resp = await api.redeem_reward(prm)
                
                context.commit('set_common', ['redeem', false])
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                    context.commit('set_common', ['redeem_text', resp.message])
                    context.commit('set_common', ['snackbar', true])
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.dispatch("search_customer", {})
                    context.dispatch("redeem/search", {}, {root:true})
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                context.commit('set_common', ['redeem', false])
                console.log(e)
            }
        }
    }
}