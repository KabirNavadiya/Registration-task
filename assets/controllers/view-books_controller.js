import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        const url = this.data.get('url');
        $('#booksTable').DataTable({
            ajax: url,
            columns: [
                { data: 'title' },
                { data: 'author' },
                { data: 'isAvailable' },
                {
                    data: 'actions',
                    render: function (data) {
                        return `
                            <a href="${data[0]}" class="btn btn-sm btn-success me-1">Edit</a>
                            <a href="${data[1]}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this book?');">Delete</a>
                        `;
                    }
                }
            ]
        });
    }
}
