var Comments = {
    //Attributs
    msgFlash : document.getElementById("success"),
    masterComment : [],
    classCommentsElt : null,
    linkUpElt : null,
    upClickListComments: null,
    upClickParentListComments : null,
    downClickListComments : null,
    downClickParentListComment : null,
    displayCommentElt : null,
    stringReducing : null,
    upClickComment : null,

    appearanceDisappearanceMsgFlash : function() {
        if(this.msgFlash !== null)
        {
            setTimeout(function() {
                Comments.msgFlash.style.display = "block"; // Fait apparaitre le message flash aprés 1 seconde
            }, 1000);

            setTimeout(function() {
                Comments.msgFlash.style.display = "none"; // Le fait disparaitre au bout de 8 secondes
            }, 8000);
        }
    },

    hideComments : function() {
        this.classCommentsElt = document.getElementsByClassName("comments");

        for(var i = 0; i < this.classCommentsElt.length; i++)
        {
            // Réduit le conteneur des commentaires
            if(this.classCommentsElt[i].offsetHeight > 300)
            {
                this.linkUpElt = document.createElement("a"); // Crée un lien qui permettra d'afficher le restant des commentaires
                this.linkUpElt.id = "up"; // Définie l'identifiant de l'élément
                this.linkUpElt.textContent = "Afficher tous les commentaires"; // Contenu textuel du lien

                this.classCommentsElt[i].style.height = "280px"; // On fixe une taille
                this.classCommentsElt[i].style.overflow = "hidden"; // On cache ce qui dépasse;
                this.classCommentsElt[i].insertAdjacentHTML("afterEnd", '<span id="up'+i+'" class="up" onclick="Comments.upListComments('+i+');">Afficher tous les commentaires</span>');
            }
        }
    },

    upListComments : function(id) {
        this.upClickListComments = document.getElementById("up"+id); // Selectionne le span up qui à était cliquer
        this.upClickParentListComments = this.upClickListComments.parentNode.querySelector(".comments"); // Récupére l'élément .comments à partir du parent
        this.upClickParentListComments.style.height = "100%"; // Agrandi l'élément .comments
        this.upClickListComments.style.display = "none";
        this.upClickParentListComments.insertAdjacentHTML("afterEnd", '<span id="down'+id+'" class="down" onclick="Comments.downListComments('+id+');">Cacher les commentaires</span>');
    },

    downListComments : function(id) {
        this.downClickListComments = document.getElementById("down"+id); // Selectionne le span down qui à était cliquer
        this.upClickListComments = document.getElementById("up"+id); // Selectionne le span up qui à était cacher
        this.downClickParentListComment = this.downClickListComments.parentNode.querySelector(".comments"); // Récupére l'élément .comments à partir du parent

        this.downClickParentListComment.style.height = "280px"; // Réduit l'élément .comments
        this.downClickListComments.style.display = "none";
        this.upClickListComments.style.display = "block";
    },

    cutComment : function() {
        this.displayCommentElt = document.querySelectorAll(".displayComment > p");

        for(var item = 0; item < this.displayCommentElt.length; item++) // Parcour tout les éléments trouver
        {
            if(this.displayCommentElt[item].offsetHeight > 100) //Si le conteneur d'un commentaire fait plus de 100px de hauteur
            {
                // Enregistre le commentaire originale avec "item" comme clé
                this.masterComment[item] = this.displayCommentElt[item].textContent;
                // Coupe le commentaire, s'assure qu'il n'y a pas d'espace à la fin de la chaine et ajoute "..."
                this.stringReducing = this.displayCommentElt[item].textContent.substr(0, 200).trim()+"...";
                this.displayCommentElt[item].textContent = this.stringReducing; // Remplace le commentaire par le commentaire couper
                // Ajoute l'élément qui servira à afficher le restant du commentaire
                this.displayCommentElt[item].insertAdjacentHTML("beforeEnd", '<span id="upComment'+item+'" class="upComment" onclick="Comments.upComment('+item+');">Afficher plus</span>');
            }
        }
    },

    upComment : function(id) {
        this.upClickComment = document.getElementById("upComment"+id);
        this.upClickComment.parentNode.textContent = this.masterComment[id];
    }
}

Comments.appearanceDisappearanceMsgFlash(); // Lance la méthode des messages flash

Comments.hideComments();

Comments.cutComment();