import { reactive, watch } from "vue";
import { router } from "@inertiajs/vue3";
import debounce from "lodash/debounce";

export const useFilters = (filtersOn, routeName, isLoading) => {
    const filters = reactive({
        search: filtersOn.search,
        order: filtersOn.order,
        rows: filtersOn.rows,
        statuses: filtersOn.statuses,
        direction: filtersOn.direction,
    });

    const applyFilters = (preserveState) => {
        isLoading.value = true;
        router.get(route(`${routeName}index`), filters, {
            preserveState: preserveState,
            replace: true,
            onFinish: () => {
                isLoading.value = false;
            }
        });
    };

    const clearFilters = () => {
        router.get(route(`${routeName}index`), {
            replace: true,
        });
    };

    const sortByColumn = (order, direction) => {
        filters.order = order;
        filters.direction = direction;
        applyFilters(true);
    };

    watch(
        () => filters.search,
        debounce(function () {
            applyFilters(true);
        }, 500)
    );

    return {
        //
        filters,
        // methods
        applyFilters,
        clearFilters,
        sortByColumn,
    };
};
