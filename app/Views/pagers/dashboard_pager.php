<?php $pager->setSurroundCount(2); ?>

<?php if ($pager->hasPrevious() || $pager->hasNext() || count($pager->links()) > 1): ?>

    <nav class="table-pagination">

        <span class="pagination-summary">

            Showing
            <?= (($pager->getCurrentPage() - 1) * $pager->getPerPage()) + 1; ?>
            -
            <?= min($pager->getCurrentPage() * $pager->getPerPage(), $pager->getTotal()); ?>
            of
            <?= $pager->getTotal(); ?>

        </span>

        <ul class="pagination mb-0">

            <?php if ($pager->hasPreviousPage()): ?>

                <li class="page-item">

                    <a class="page-link" href="<?= $pager->getPreviousPageURI() ?>">

                        <i class="bi bi-chevron-left"></i>

                    </a>

                </li>

            <?php else: ?>

                <li class="page-item disabled">

                    <span class="page-link">

                        <i class="bi bi-chevron-left"></i>

                    </span>

                </li>

            <?php endif; ?>


            <?php foreach ($pager->links() as $link): ?>

                <li class="page-item <?= $link['active'] ? 'active' : '' ?>">

                    <a class="page-link" href="<?= $link['uri'] ?>">

                        <?= $link['title'] ?>

                    </a>

                </li>

            <?php endforeach; ?>


            <?php if ($pager->hasNextPage()): ?>

                <li class="page-item">

                    <a class="page-link" href="<?= $pager->getNextPageURI() ?>">

                        <i class="bi bi-chevron-right"></i>

                    </a>

                </li>

            <?php else: ?>

                <li class="page-item disabled">

                    <span class="page-link">

                        <i class="bi bi-chevron-right"></i>

                    </span>

                </li>

            <?php endif; ?>

        </ul>

    </nav>

<?php endif; ?>