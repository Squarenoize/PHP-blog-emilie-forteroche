<?php 
/**
 * Contrôleur de la partie admin.
 */
 
class AdminController {

    /**
     * Affiche la page d'administration.
     * @return void
     */
    public function showAdmin() : void
    {
        // On vérifie que l'utilisateur est connecté.
        $this->checkIfUserIsConnected();

        // On récupère le panel demandé (édition ou monitoring).
        $panel = Utils::request("panel", "edition");
        
        switch ($panel) {
            case "monitoring":
                $monitoring = true;
                $articleManager = new ArticleManager();
                $articles = $articleManager->getAllArticles($monitoring);
                
                $sort = Utils::request("sort", "date_desc");
                $allowedSorts = [
                    "date_desc",
                    "date_asc",
                    "views_desc",
                    "views_asc",
                    "comments_desc",
                    "comments_asc",
                    "title_asc",
                    "title_desc"
                ];
                if (!in_array($sort, $allowedSorts, true)) {
                    $sort = "date_desc";
                }

                $sortLinks = [
                    "title" => Utils::getNextSort($sort, "title"),
                    "views" => Utils::getNextSort($sort, "views"),
                    "comments" => Utils::getNextSort($sort, "comments"),
                    "date" => Utils::getNextSort($sort, "date")
                ];

                $sortIcons = [
                    "title" => Utils::getSortIcon($sort, "title"),
                    "views" => Utils::getSortIcon($sort, "views"),
                    "comments" => Utils::getSortIcon($sort, "comments"),
                    "date" => Utils::getSortIcon($sort, "date")
                ];
                
                $articles = $this->sortArticles($articles, $sort);
                
                $panelView = "adminMonitoring";
                $view = new View("Administration");
                $view->render($panelView, [
                    'articles' => $articles,
                    'panel' => $panel,
                    'sort' => $sort,
                    'sortLinks' => $sortLinks,
                    'sortIcons' => $sortIcons
                ]);
                break;
            case "edition":
                $articleManager = new ArticleManager();
                $articles = $articleManager->getAllArticles();
                
                $panelView = "adminEdition";
                // On affiche la page d'administration.
                $view = new View("Administration");
                $view->render($panelView, [
                    'articles' => $articles,
                    'panel' => $panel
                ]);
                break;
        }  
    }

    /**
     * Suppression d'un commentaire. Accessible uniquement aux admins (vérification dans la méthode).
     * @return void
     */
    public function deleteComment() : void 
    {
        // Vérification que l'utilisateur est connecté et est un admin.
        $this->checkIfUserIsConnected();

        // Récupération de l'id du commentaire à supprimer.
        $id = Utils::request("id", -1);

        // Récupération du commentaire à supprimer.
        $commentManager = new CommentManager();
        $comment = $commentManager->getCommentById($id);
        if (!$comment) {
            throw new Exception("Le commentaire demandé n'existe pas.");
        }

        // Suppression du commentaire.
        $result = $commentManager->deleteComment($comment);
        if (!$result) {
            throw new Exception("Une erreur est survenue lors de la suppression du commentaire.");
        }

        // Redirection vers la page de l'article associé au commentaire supprimé.
        Utils::redirect("showArticle", ['id' => $comment->getIdArticle()]);
    }

    /**
     * Vérifie que l'utilisateur est connecté.
     * @return void
     */
    private function checkIfUserIsConnected() : void
    {
        // On vérifie que l'utilisateur est connecté.
        if (!isset($_SESSION['user'])) {
            Utils::redirect("connectionForm");
        }
    }

    /**
     * Affichage du formulaire de connexion.
     * @return void
     */
    public function displayConnectionForm() : void 
    {
        $view = new View("Connexion");
        $view->render("connectionForm");
    }

    /**
     * Connexion de l'utilisateur.
     * @return void
     */
    public function connectUser() : void 
    {
        // On récupère les données du formulaire.
        $login = Utils::request("login");
        $password = Utils::request("password");

        // On vérifie que les données sont valides.
        if (empty($login) || empty($password)) {
            throw new Exception("Tous les champs sont obligatoires. 1");
        }

        // On vérifie que l'utilisateur existe.
        $userManager = new UserManager();
        $user = $userManager->getUserByLogin($login);
        if (!$user) {
            throw new Exception("L'utilisateur demandé n'existe pas.");
        }

        // On vérifie que le mot de passe est correct.
        if (!password_verify($password, $user->getPassword())) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            throw new Exception("Le mot de passe est incorrect : $hash");
        }

        // On connecte l'utilisateur.
        $_SESSION['user'] = $user;
        $_SESSION['idUser'] = $user->getId();

        // On redirige vers la page d'administration.
        Utils::redirect("admin");
    }

    /**
     * Déconnexion de l'utilisateur.
     * @return void
     */
    public function disconnectUser() : void 
    {
        // On déconnecte l'utilisateur.
        unset($_SESSION['user']);

        // On redirige vers la page d'accueil.
        Utils::redirect("home");
    }

    /**
     * Affichage du formulaire d'ajout d'un article.
     * @return void
     */
    public function showUpdateArticleForm() : void 
    {
        $this->checkIfUserIsConnected();

        // On récupère l'id de l'article s'il existe.
        $id = Utils::request("id", -1);

        // On récupère l'article associé.
        $articleManager = new ArticleManager();
        $article = $articleManager->getArticleById($id);

        // Si l'article n'existe pas, on en crée un vide. 
        if (!$article) {
            $article = new Article();
        }

        // On affiche la page de modification de l'article.
        $view = new View("Edition d'un article");
        $view->render("updateArticleForm", [
            'article' => $article
        ]);
    }

    /**
     * Ajout et modification d'un article. 
     * On sait si un article est ajouté car l'id vaut -1.
     * @return void
     */
    public function updateArticle() : void 
    {
        $this->checkIfUserIsConnected();

        // On récupère les données du formulaire.
        $id = Utils::request("id", -1);
        $title = Utils::request("title");
        $content = Utils::request("content");

        // On vérifie que les données sont valides.
        if (empty($title) || empty($content)) {
            throw new Exception("Tous les champs sont obligatoires. 2");
        }

        // On crée l'objet Article.
        $article = new Article([
            'id' => $id, // Si l'id vaut -1, l'article sera ajouté. Sinon, il sera modifié.
            'title' => $title,
            'content' => $content,
            'id_user' => $_SESSION['idUser']
        ]);

        // On ajoute l'article.
        $articleManager = new ArticleManager();
        $articleManager->addOrUpdateArticle($article);

        // On redirige vers la page d'administration.
        Utils::redirect("admin");
    }


    /**
     * Suppression d'un article.
     * @return void
     */
    public function deleteArticle() : void
    {
        $this->checkIfUserIsConnected();

        $id = Utils::request("id", -1);

        // On supprime l'article.
        $articleManager = new ArticleManager();
        $articleManager->deleteArticle($id);
       
        // On redirige vers la page d'administration.
        Utils::redirect("admin");
    }

    private function sortArticles(array $articles, string $sort) : array 
    {
        switch ($sort) {
            case "title_asc":
                usort($articles, function($a, $b) {
                    return $b->getTitle() <=> $a->getTitle();
                });
                break;
            case "title_desc":
                usort($articles, function($a, $b) {
                    return $a->getTitle() <=> $b->getTitle();
                });
                break;
            case "date_asc":
                usort($articles, function($a, $b) {
                    return $a->getDateCreation() <=> $b->getDateCreation();
                });
                break;
            case "date_desc":
                usort($articles, function($a, $b) {
                    return $b->getDateCreation() <=> $a->getDateCreation();
                });
                break;
            case "views_asc":
                usort($articles, function($a, $b) {
                    return $a->getViews() <=> $b->getViews();
                });
                break;
            case "views_desc":
                usort($articles, function($a, $b) {
                    return $b->getViews() <=> $a->getViews();
                });
                break;
            case "comments_asc":
                usort($articles, function($a, $b) {
                    return $a->getCommentsCount() <=> $b->getCommentsCount();
                });
                break;
            case "comments_desc":
                usort($articles, function($a, $b) {
                    return $b->getCommentsCount() <=> $a->getCommentsCount();
                });
                break;
        }
        return $articles;
    }
}