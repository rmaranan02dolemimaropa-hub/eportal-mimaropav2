<?= $this->include('dashboard_layout/head') ?>
<?= $this->include('dashboard_layout/navbar') ?>
<?= $this->include('dashboard_layout/sidebar') ?>


<!-- ============ MAIN CONTENT ============ -->
<main class="main-content">

    <!-- Recent Requests Table -->
    <section class="panel table-panel">
        <div class="panel-header">
            <h2 class="panel-title">Recent Service Requests</h2>
            <div class="panel-tools">
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control" placeholder="Search requests..." aria-label="Search requests" />
                </div>
                <button class="btn tool-btn" type="button">
                    <i class="bi bi-funnel"></i><span class="d-none d-md-inline">Filter</span>
                </button>
                <button class="btn tool-btn" type="button">
                    <i class="bi bi-download"></i><span class="d-none d-md-inline">Export</span>
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table requests-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Reference No.</th>
                        <th>Requestor</th>
                        <th>Service</th>
                        <th>Date Requested</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="ref-no">HR-202607240001</td>
                        <td>Juan Dela Cruz</td>
                        <td>Certificate of Employment</td>
                        <td>July 24, 2026</td>
                        <td><span class="badge badge-pending">Pending</span></td>
                        <td class="text-end">
                            <div class="row-actions">
                                <button class="row-action-btn" title="View"><i class="bi bi-eye"></i></button>
                                <button class="row-action-btn" title="Edit"><i class="bi bi-pencil"></i></button>
                                <button class="row-action-btn row-action-danger" title="Delete"><i class="bi bi-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="ref-no">HR-202607230014</td>
                        <td>Maria Santos</td>
                        <td>Service Record</td>
                        <td>July 23, 2026</td>
                        <td><span class="badge badge-completed">Completed</span></td>
                        <td class="text-end">
                            <div class="row-actions">
                                <button class="row-action-btn" title="View"><i class="bi bi-eye"></i></button>
                                <button class="row-action-btn" title="Edit"><i class="bi bi-pencil"></i></button>
                                <button class="row-action-btn row-action-danger" title="Delete"><i class="bi bi-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="ref-no">HR-202607230009</td>
                        <td>Ramon Villareal</td>
                        <td>Leave Application</td>
                        <td>July 23, 2026</td>
                        <td><span class="badge badge-processing">Processing</span></td>
                        <td class="text-end">
                            <div class="row-actions">
                                <button class="row-action-btn" title="View"><i class="bi bi-eye"></i></button>
                                <button class="row-action-btn" title="Edit"><i class="bi bi-pencil"></i></button>
                                <button class="row-action-btn row-action-danger" title="Delete"><i class="bi bi-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="ref-no">HR-202607220031</td>
                        <td>Angelica Reyes</td>
                        <td>Certificate of Employment</td>
                        <td>July 22, 2026</td>
                        <td><span class="badge badge-released">Released</span></td>
                        <td class="text-end">
                            <div class="row-actions">
                                <button class="row-action-btn" title="View"><i class="bi bi-eye"></i></button>
                                <button class="row-action-btn" title="Edit"><i class="bi bi-pencil"></i></button>
                                <button class="row-action-btn row-action-danger" title="Delete"><i class="bi bi-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="ref-no">HR-202607210007</td>
                        <td>Noel Bautista</td>
                        <td>Clearance Request</td>
                        <td>July 21, 2026</td>
                        <td><span class="badge badge-rejected">Rejected</span></td>
                        <td class="text-end">
                            <div class="row-actions">
                                <button class="row-action-btn" title="View"><i class="bi bi-eye"></i></button>
                                <button class="row-action-btn" title="Edit"><i class="bi bi-pencil"></i></button>
                                <button class="row-action-btn row-action-danger" title="Delete"><i class="bi bi-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <nav class="table-pagination" aria-label="Requests pagination">
            <span class="pagination-summary">Showing 1–5 of 128 requests</span>
            <ul class="pagination mb-0">
                <li class="page-item disabled"><a class="page-link" href="#" tabindex="-1"><i class="bi bi-chevron-left"></i></a></li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item"><a class="page-link" href="#"><i class="bi bi-chevron-right"></i></a></li>
            </ul>
        </nav>
    </section>
</main>
</div>


<?= $this->include('dashboard_layout/footer') ?>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const currentPath = window.location.pathname.replace(/\/+$/, '');

        const navLinks = document.querySelectorAll('.nav-link:not(.nav-link-logout)');

        navLinks.forEach(link => {
            const href = link.getAttribute('href');

            // Skip placeholder links entirely
            if (!href || href === '#') {
                link.classList.remove('active');
                return;
            }

            const linkPath = new URL(link.href).pathname.replace(/\/+$/, '');

            const isMatch =
                currentPath === linkPath ||
                currentPath.startsWith(linkPath + '/');

            link.classList.toggle('active', isMatch);
        });
    });
</script>