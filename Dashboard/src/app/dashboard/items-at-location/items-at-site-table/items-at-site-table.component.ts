import {Component, Input, OnChanges, OnInit, SimpleChanges} from '@angular/core';

@Component({
  selector: 'cintas-items-at-site-table',
  templateUrl: './items-at-site-table.component.html',
  styleUrls: ['./items-at-site-table.component.scss']
})
export class ItemsAtSiteTableComponent implements OnInit, OnChanges {

  @Input()
  noItemsPerProduct = [];

  totalNoItemsAtSite = 0;

  constructor() {
  }

  ngOnInit() {
  }

  ngOnChanges(changes: SimpleChanges): void {
    if (changes.noItemsPerProduct && this.noItemsPerProduct) {
      for (let i of this.noItemsPerProduct) {
        this.totalNoItemsAtSite += i.value;
      }
    }
  }

}
