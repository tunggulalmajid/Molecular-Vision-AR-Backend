import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export function usePermission() {
    const page = usePage();

    const roles = computed<string[]>(() => {
        return (page.props.auth?.roles as string[]) || [];
    });

    const permissions = computed<string[]>(() => {
        return (page.props.auth?.permissions as string[]) || [];
    });

    const isSuperAdmin = computed<boolean>(() => {
        return roles.value.some((r) => r.toLowerCase() === 'super admin');
    });

    /**
     * Check if current user has a specific permission or is super admin.
     */
    const can = (permission: string): boolean => {
        if (isSuperAdmin.value) return true;
        return permissions.value.includes(permission);
    };

    /**
     * Check if current user has a specific role.
     */
    const hasRole = (role: string): boolean => {
        return roles.value.some((r) => r.toLowerCase() === role.toLowerCase());
    };

    /**
     * Check if current user has any of the given permissions.
     */
    const canAny = (permissionList: string[]): boolean => {
        if (isSuperAdmin.value) return true;
        return permissionList.some((p) => permissions.value.includes(p));
    };

    return {
        roles,
        permissions,
        isSuperAdmin,
        can,
        hasRole,
        canAny,
    };
}
