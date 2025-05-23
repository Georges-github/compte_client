<?php
    
namespace App\Service;

use App\Entity\Commentaire;

class CommentaireTreeBuilder
{
    /**
     * Construit un arbre de commentaires récursifs à partir d'une liste plate.
     *
     * @param Commentaire[] $commentaires Liste de tous les commentaires d'une publication
     * @return array Arbre de commentaires
     */
    public function buildTree(array $commentaires): array
    {
        $tree = [];
        $index = [];

        // Initialiser les commentaires et les indexer
        foreach ($commentaires as $commentaire) {
            $id = $commentaire->getId();
            $index[$id] = $commentaire;
            $commentaire->children = []; // Propriété dynamique pour twig
        }

        // Organiser les commentaires par parent
        foreach ($commentaires as $commentaire) {
            $parent = $commentaire->getIdCommentaireParent();
            if ($parent !== null && isset($index[$parent->getId()])) {
                $index[$parent->getId()]->children[] = $commentaire;
            } else {
                $tree[] = $commentaire; // Pas de parent, donc racine
            }
        }

        return $tree;
    }
}
