// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_expense.js"
import { one_token } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        search_status: 0,
        search_error_message: '',
        search: '',
        
        expenses: [],
        total_expense: 0,
        total_expense_page: 0,
        selected_expense: {},

        current_page: 1
    },
    mutations: {
        update_search_error_message(state, msg) {
            state.search_error_message = msg
        },

        update_search(state, search) {
            state.search = search
        },

        update_expenses(state, data) {
            state.expenses = data
        },

        set_selected_expense(state, val) {
            state.selected_expense = val
        },

        update_search_status(state, val) {
            state.search_status = val
        },

        update_total_expense(state, val) {
            state.total_expense = val
        },

        update_total_expense_page(state, val) {
            state.total_expense_page = val
        },

        update_current_page(state, val) {
            state.current_page = val
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

                    context.commit("update_expenses", data.records)
                    context.commit("update_total_expense", data.total)
                    context.commit("update_total_expense_page", data.total_page)
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
                prm.id = context.state.selected_expense.M_ExpenseID

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
        }
    }
}