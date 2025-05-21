import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        const url = this.data.get('url');
        $('#availablebooksTable').DataTable({
            ajax: url,
            order: [],
            columns: [
                { data: 'title' },
                { data: 'author' },
                {
                    data: 'actions',
                    render: function (data) {
                        return `
                            <a href="${data}" class="btn btn-sm btn-success me-1">Issue</a>
                        `;
                    }
                }
            ],
            columnDefs: [
                {
                    targets: [0, 1],
                    orderSequence: ['asc', 'desc', '']
                },
                { orderable: false, targets: 2 }
            ],

        });
    }
}
