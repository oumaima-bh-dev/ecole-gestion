/**
 * Main JS Helper Script
 */
document.addEventListener("DOMContentLoaded", function () {
    setupAlerts();
    setupClassFilter();
});

function setupAlerts() {
    const alerts = document.querySelectorAll(".alert-dismissible");

    alerts.forEach(function (alert) {
        setTimeout(function () {
            if (window.bootstrap && bootstrap.Alert) {
                bootstrap.Alert.getOrCreateInstance(alert).close();
                return;
            }

            alert.remove();
        }, 4000);
    });
}

function setupClassFilter() {
    const classFilter = document.getElementById("classSelectFilter");

    if (!classFilter) {
        return;
    }

    classFilter.addEventListener("change", function () {
        const currentUrl = new URL(window.location.href);

        if (this.value) {
            currentUrl.searchParams.set("class_id", this.value);
        } else {
            currentUrl.searchParams.delete("class_id");
        }

        window.location.href = currentUrl.toString();
    });
}

function printReceipt() {
    window.print();
}
