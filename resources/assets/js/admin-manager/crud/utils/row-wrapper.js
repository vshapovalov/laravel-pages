export default class RowWrapper{
    constructor(item){
        this.item = item;
        this.isSelected = false;
    }

    getSelected(){
        return this.isSelected;
    }
}
