import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        this.count = 0;


        this.element.addEventListener('click', () => {
            this.count++;
            this.element.innerHTML = this.count;
        });
    }
}