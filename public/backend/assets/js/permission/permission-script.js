(function () {
    "use strict";
    window.loadUserPermissions = async function(userId) {
        try {
            const response = await fetch(`/admin/get-user-permissions`);
            const data = await response.json();
            if (data.code === 200) {
                return data.data;
            } else {
                showToast('error', 'Failed to load permissions:', data.message);
                return [];
            }
        } catch (error) {
            showToast('error', 'Error fetching user permissions:', error);
            return [];
        }
    }

    window.hasPermission = function (permissions, moduleSlug, action) {
        if ($('body').data('user-type') == 1) {
            return true;
        }

        if (!permissions || permissions.length === 0) {
            return false;
        }

        const moduleSlugs = Array.isArray(moduleSlug) ? moduleSlug : [moduleSlug];

        for (let slug of moduleSlugs) {
            const permission = permissions.find(perm => perm.module.module_slug === slug);

            if (permission && permission[action] === 1) {
                return true;
            }
        }

        return false;
    }
})();
