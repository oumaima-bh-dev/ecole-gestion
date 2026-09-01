<div class="container-fluid p-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold">Mes documents</h1>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <i class="fa-solid fa-tags me-1"></i> Catégorie
            </button>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDocumentModal" <?php echo empty($categories) || empty($assignments) ? 'disabled' : ''; ?>>
                <i class="fa-solid fa-file-arrow-up me-1"></i> Ajouter
            </button>
        </div>
    </div>

    <?php if (empty($assignments)): ?>
        <div class="alert alert-info py-4 text-center">
            <i class="fa-solid fa-circle-info fs-2 mb-2"></i>
            <p class="m-0">Vous devez avoir une classe affectée avant de publier des documents.</p>
        </div>
    <?php endif; ?>

    <?php if (empty($categories)): ?>
        <div class="alert alert-warning py-3">
            <i class="fa-solid fa-folder-plus me-2"></i> Créez d'abord une catégorie pour organiser vos documents.
        </div>
    <?php endif; ?>

    <div class="custom-table-container">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3 border-bottom pb-3">
            <h5 class="fw-bold m-0"><i class="fa-solid fa-folder-open text-primary me-2"></i>Documents publiés</h5>
            <span class="text-muted small">Total : <?php echo count($documents); ?> document(s)</span>
        </div>

        <div class="table-responsive mb-4">
            <table class="table table-custom align-middle">
                <thead>
                    <tr class="table-light">
                        <th class="ps-4 text-uppercase text-secondary small tracking-wider">Document</th>
                        <th class="text-uppercase text-secondary small tracking-wider">Catégorie</th>
                        <th class="text-uppercase text-secondary small tracking-wider">Classe</th>
                        <th class="text-uppercase text-secondary small tracking-wider">Statut</th>
                        <th class="pe-4 text-end text-uppercase text-secondary small tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($documents)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Aucun document ajouté pour le moment.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($documents as $document): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark fs-6"><?php echo htmlspecialchars($document['titre']); ?></div>
                                    <div class="text-muted small"><?php echo htmlspecialchars($document['description'] ?: $document['original_name']); ?></div>
                                </td>
                                <td><span class="badge bg-primary-soft text-primary"><?php echo htmlspecialchars($document['category_name']); ?></span></td>
                                <td class="text-muted"><?php echo htmlspecialchars($document['class_name']); ?></td>
                                <td>
                                    <?php if ($document['status'] === 'active'): ?>
                                        <span class="badge bg-success-soft">Actif</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-soft">Désactivé</span>
                                    <?php endif; ?>
                                </td>
                                <td class="pe-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="index.php?controller=teacher&action=viewDocument&id=<?php echo $document['id']; ?>" class="btn btn-sm btn-outline-primary" title="Consulter" target="_blank">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="index.php?controller=teacher&action=downloadDocument&id=<?php echo $document['id']; ?>" class="btn btn-sm btn-outline-secondary" title="Télécharger">
                                            <i class="fa-solid fa-download"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editDocumentModal<?php echo $document['id']; ?>" title="Modifier">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <form action="index.php?controller=teacher&action=documents" method="POST">
                                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                            <input type="hidden" name="action" value="toggle_document">
                                            <input type="hidden" name="id" value="<?php echo $document['id']; ?>">
                                            <button type="submit" class="btn btn-sm <?php echo $document['status'] === 'active' ? 'btn-outline-warning' : 'btn-outline-success'; ?>" title="<?php echo $document['status'] === 'active' ? 'Désactiver' : 'Réactiver'; ?>">
                                                <i class="fa-solid <?php echo $document['status'] === 'active' ? 'fa-eye-slash' : 'fa-eye'; ?>"></i>
                                            </button>
                                        </form>
                                        <form action="index.php?controller=teacher&action=documents" method="POST" onsubmit="return confirm('Voulez-vous supprimer définitivement ce document ?');">
                                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                            <input type="hidden" name="action" value="delete_document">
                                            <input type="hidden" name="id" value="<?php echo $document['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form class="modal-content" action="index.php?controller=teacher&action=documents" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="action" value="add_category">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Créer une catégorie</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label class="form-label">Nom</label>
                <input type="text" class="form-control" name="nom" placeholder="Ex: Cours, Exercices, Corrections..." required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="addDocumentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" action="index.php?controller=teacher&action=documents" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="action" value="add_document">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Ajouter un document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php include dirname(__DIR__) . '/teacher/partials/document_form.php'; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-primary">Publier</button>
            </div>
        </form>
    </div>
</div>

<?php foreach ($documents as $document): ?>
    <div class="modal fade" id="editDocumentModal<?php echo $document['id']; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form class="modal-content" action="index.php?controller=teacher&action=documents" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="action" value="edit_document">
                <input type="hidden" name="id" value="<?php echo $document['id']; ?>">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Modifier le document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php $currentDocument = $document; include dirname(__DIR__) . '/teacher/partials/document_form.php'; unset($currentDocument); ?>
                    <div class="text-muted small mt-2">Laissez le fichier vide pour conserver : <?php echo htmlspecialchars($document['original_name']); ?></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
<?php endforeach; ?>
