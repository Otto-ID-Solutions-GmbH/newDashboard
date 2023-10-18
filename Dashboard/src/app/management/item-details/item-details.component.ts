import {Component, OnInit, PipeTransform, ViewChild} from '@angular/core';
import {FeatureSet} from "../../core/statistics-page-menu/statistics-page-menu.component";
import {Page} from "../../core/models/page.model";
import {SortInfo} from "../../core/models/sort.mode";
import {ItemService} from "../services/item.service";
import {ActivatedRoute} from "@angular/router";
import {DatePipe} from "@angular/common";
import {environment} from "../../../environments/environment";

class TimeStampPipe extends DatePipe {
  public transform(value): any {
    return super.transform(value, 'long', environment.theme.timezone);
  }
}

@Component({
  selector: 'cintas-item-details',
  templateUrl: './item-details.component.html',
  styleUrls: ['./item-details.component.scss']
})
export class ItemDetailsComponent implements OnInit {

  itemCuid;

  @ViewChild('dataTable', { static: true }) table: any;

  features: FeatureSet = {
    export: false
  };

  dataLoading;

  data;

  page = new Page();
  sort = new SortInfo();
  pipe: PipeTransform;

  rows = [];

  columns = [
    {name: 'Status Timestamp', prop: 'created_at', pipe: new TimeStampPipe('en-US'), sortable: false},
    {name: 'Status', prop: 'label', sortable: false},
  ];

  dataSub;
  paramSub;

  constructor(private itemService: ItemService, private route: ActivatedRoute) {
  }

  ngOnInit() {
    this.setPage({offset: 0});
    this.paramSub = this.route.params.subscribe(p => {
      this.itemCuid = p.itemCuid;
      this.loadData();
    });
  }

  /**
   * Populate the table with new data based on the page number
   * @param pageInfo The page to select
   */
  setPage(pageInfo) {
    this.page.pageNumber = pageInfo.offset;
    this.loadData();
  }

  onSort(event) {
    this.sort = new SortInfo(event.sorts[0].prop, event.sorts[0].dir);
    this.loadData();
  }

  loadData() {
    if (!this.itemCuid) {
      this.rows = [];
      return;
    }
    this.dataLoading = true;
    this.dataSub = this.itemService.getItemStatuses(this.itemCuid).subscribe((res: any) => {
      this.dataLoading = false;
      this.data = res.data;
      this.rows = this.data.status_history || [];

      this.page = new Page(res.meta);
      this.table.bodyComponent.updateOffsetY(this.page.pageNumber);
      this.table.offset = this.page.pageNumber;
    });
  }

  ngOnDestroy(): void {
    if (this.dataSub) {
      this.dataSub.unsubscribe();
    }
  }

}
