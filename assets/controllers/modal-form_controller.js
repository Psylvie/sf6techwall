import { Controller } from '@hotwired/stimulus';
import {Modal} from "bootstrap";
import $ from 'jquery';
import {useDispatch} from 'stimulus-use';

export default class extends Controller {

// Définition des éléments cibles et des valeurs statiques pour ce contrôleur
    static targets = ['modal', 'modalBody'];// Éléments cibles du DOM accessibles par this.modalTarget et this.modalBodyTarget
    static values = {
        formUrl: String,// Définit une valeur dynamique 'formUrl' avec le type 'String'
    }

// Méthode appelée lorsque ce contrôleur est connecté à un élément du DOM
    connect() {
        useDispatch(this, {debug: true});
    }

    modal = null;
// Méthode asynchrone pour ouvrir une fenêtre modal
    async openModal(event) {
        event.preventDefault();// Empêche le comportement par défaut
       this.modalBodyTarget.innerHTML = 'Loading...';// Affiche "Loading..." dans le corps de la fenêtre modale

// Crée une nouvelle instance de la classe Modal de Bootstrap en utilisant l'élément cible 'modalTarget'
        const modal = new Modal(this.modalTarget);
        modal.show();// Affiche la fenêtre modale

// Charge le contenu de la fenêtre modal à partir de l'URL spécifiée dans 'formUrlValue' en utilisant AJAX
        this.modalBodyTarget.innerHTML = await $.ajax(this.formUrlValue);
    }

// Méthode asynchrone pour soumettre le form dans la modal
    async submitForm() {
        event.preventDefault();// Empêche le comportement par défaut

// Sélectionne le form à l'intérieur de la modal en utilisant jQuery
        const $form = $(this.modalBodyTarget).find('form');

        try{
// Effectue une requête AJAX pour soumettre le form
            await $.ajax({
// URL à laquelle la requête AJAX sera envoyée, obtenue à partir de la valeur 'formUrlValue'
                url: this.formUrlValue,
// Méthode HTTP à utiliser pour la requête, récupérée depuis le formulaire HTML
                method: $form.prop('method'),
// Les données à envoyer avec la requête, sérialisées à partir du formulaire HTML
                data: $form.serialize(),

            });
            const contentTarget = this.data.get('reload-content-target');
            if (contentTarget) {
                const content = document.querySelector(contentTarget);
                if (content) {
                    content.innerHTML = await $.ajax(this.formUrlValue);
                }
            }
            this.modal.hide();// Cache la modale après la soumission réussie
            this.dispatch('success')// Déclenche un événement nommé 'success' via Stimulus
        }catch (e){
            // En cas d'erreur, affiche la réponse d'erreur dans le body de la modal
            this.modalBodyTarget.innerHTML = e.responseText;
        }

    }
    // Méthode asynchrone appelée quand la modale est masquée
    async modalHidden(){
        console.log('it was hidden!')// Affiche un message dans la console quand la modal est masquée
    }
    // Méthode pour rafraîchir la liste des pièces jointes
    async refreshAttachmentList() {
        try {
            const contentTarget = this.data.get('reload-content-target');
            if (contentTarget) {
                const content = document.querySelector(contentTarget);
                if (content) {
                    const response = await $.ajax(this.formUrlValue);
                    content.innerHTML = response;
                }
            }
        } catch (error) {
            console.error('Error refreshing attachment list:', error);
        }
    }
}