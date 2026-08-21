if (typeof window.formatDate !== 'function') {
    window.formatDate = function (dateStr) {
        if (!dateStr || dateStr === 'N/A' || dateStr === '-') return '-';

        if (typeof dateStr === 'string' && /^\d{4}-\d{2}-\d{2}$/.test(dateStr.trim())) {
            let parts = dateStr.trim().split('-');
            return `${parts[2]}-${parts[1]}-${parts[0]}`;
        }

        let d = new Date(dateStr);
        if (isNaN(d.getTime())) return dateStr;

        let day = String(d.getDate()).padStart(2, '0');
        let month = String(d.getMonth() + 1).padStart(2, '0');
        let year = d.getFullYear();

        return `${day}-${month}-${year}`;
    };
}

if (typeof window.formatDateTime !== 'function') {
    window.formatDateTime = function (dateStr) {
        if (!dateStr || dateStr === 'N/A' || dateStr === '-') return '-';

        let d = new Date(dateStr);
        if (isNaN(d.getTime())) return dateStr;

        let day = String(d.getDate()).padStart(2, '0');
        let month = String(d.getMonth() + 1).padStart(2, '0');
        let year = d.getFullYear();

        let hours = d.getHours();
        let minutes = String(d.getMinutes()).padStart(2, '0');
        let ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12;
        let hoursStr = String(hours).padStart(2, '0');

        return `${day}-${month}-${year}, ${hoursStr}:${minutes} ${ampm}`;
    };
}

var formatDate = window.formatDate;
var formatDateTime = window.formatDateTime;
