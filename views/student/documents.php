<div class="container-fluid p-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 fw-bold">Mes documents</h1>
            <p class="text-muted m-0">
                <?php if (!empty($student['class_name'])): ?>
                    Documents actifs de la classe <?php echo htmlspecialchars($student['class_name']); ?>
                <?php else: ?>
                    Aucune classe n'est encore affectée à votre profil.
                <?php endif; ?>
            </p>
        </div>
    </div>

    <?php if (empty($student['class_id'])): ?>
        <div class="alert alert-info py-4 text-center">
            <i class="fa-solid fa-circle-info fs-2 mb-2"></i>
            <p class="m-0">Vos documents apparaîtront ici dès qu'une classe sera affectée à votre profil.</p>
        </div>
    <?php else: ?>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">Filtrer par catégorie</h5>
                <form action="index.php" method="GET" class="row g-3">
                    <input type="hidden" name="controller" value="student">
                    <input type="hidden" name="action" value="documents">
                    <div class="col-md-8">
                        <select class="form-select form-select-lg" name="category_id">
                            <option value="">Toutes les catégories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" <?php echo $selectedCategoryId == $category['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['nom']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary btn-lg w-100">Filtrer</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <?php if (empty($documents)): ?>
                <div class="col-12">
                    <div class="alert alert-info py-4 text-center">
                        <i class="fa-solid fa-folder-open fs-2 mb-2"></i>
                        <p class="m-0">Aucun document actif disponible pour cette sélection.</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($documents as $document): ?>
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4 d-flex flex-column">
                                <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                                    <span class="badge bg-primary-soft text-primary"><?php echo htmlspecialchars($document['category_name']); ?></span>
                                    <i class="fa-solid fa-file-lines text-muted fs-4"></i>
                                </div>
                                <h5 class="fw-bold mb-2"><?php echo htmlspecialchars($document['titre']); ?></h5>
                                <p class="text-muted small flex-grow-1"><?php echo htmlspecialchars($document['description'] ?: $document['original_name']); ?></p>
                                <div class="border-top pt-3 mt-2">
                                    <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                                        <span class="text-muted small"><i class="fa-solid fa-chalkboard-user me-1"></i><?php echo htmlspecialchars($document['teacher_name']); ?></span>
                                        <span class="text-muted small"><?php echo round($document['file_size'] / 1024, 1); ?> Ko</span>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="index.php?controller=student&action=viewDocument&id=<?php echo $document['id']; ?>" class="btn btn-outline-primary flex-fill" target="_blank">
                                            <i class="fa-solid fa-eye me-1"></i> Consulter
                                        </a>
                                        <a href="index.php?controller=student&action=downloadDocument&id=<?php echo $document['id']; ?>" class="btn btn-primary" title="Télécharger">
                                            <i class="fa-solid fa-download"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
