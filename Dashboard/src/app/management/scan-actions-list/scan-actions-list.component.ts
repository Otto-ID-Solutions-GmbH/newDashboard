import {Component, OnDestroy, OnInit, PipeTransform, ViewChild} from '@angular/core';
import {ScanActionsService} from "../services/scan-actions.service";
import {Page} from "../../core/models/page.model";
import {SortInfo} from "../../core/models/sort.mode";
import {DatePipe} from "@angular/common";
import {FeatureSet} from "../../core/statistics-page-menu/statistics-page-menu.component";
import {ExportsService} from "../../core/services/exports.service";
import {environment} from "../../../environments/environment";

class TimeStampPipe extends DatePipe {
  public transform(value): any {
    return super.transform(value, 'long', environment.theme.timezone);
  }
}

@Component({
  selector: 'cintas-scan-actions-list',
  templateUrl: './scan-actions-list.component.html',
  styleUrls: ['./scan-actions-list.component.scss']
})
export class ScanActionsListComponent implements OnInit, OnDestroy {

  @ViewChild('dataTable', { static: true }) table: any;

  features: FeatureSet = {
    export: true
  };

  dataLoading;
  exportLoading = false;
  exportDetailsLoading = false;

  page = new Page();
  sort = new SortInfo();
  pipe: PipeTransform;

  rows = [];

  columns = [
    {name: 'CUID'},
    {name: 'Action Date', prop: 'created_at', pipe: new TimeStampPipe('en-US')},
    {name: 'Type', prop: 'type_label', sortable: false},
    {name: 'Reader', prop: 'reader.label', sortable: false},
    {name: 'Total Items Count', prop: 'items_count', sortable: false},
    {name: 'Skipped Items Count', prop: 'skipped_items_count', sortable: false},
    {name: '# Unknown Tags', prop: 'unknown_tags_count', sortable: false}
  ];

  dataSub;

  constructor(private actionsService: ScanActionsService, private datePipe: DatePipe,
              private exportService: ExportsService) {
  }

  ngOnInit() {
    this.setPage({offset: 0});
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
    this.dataLoading = true;
    this.dataSub = this.actionsService.getScanActions(null, null, null, this.page.pageNumber + 1, this.sort.column, this.sort.dir).subscribe((res: any) => {
      this.dataLoading = false;
      this.rows = res.data || [];

      this.page = new Page(res.meta);
      this.table.bodyComponent.updateOffsetY(this.page.pageNumber);
      this.table.offset = this.page.pageNumber;
    });
  }

  onExport() {
    this.exportLoading = true;
    this.exportService.exportScanActionsList()
      .subscribe(response => {
        this.exportLoading = false;
        this.exportService.saveToFileSystem(response);
      }, err => {
        this.exportLoading = false;
      });
  }

  onExportDetails() {
    this.exportDetailsLoading = true;
    this.exportService.exportScanActionsDetailsList(null, null, null, this.page.pageNumber + 1, this.sort.column, this.sort.dir)
      .subscribe(response => {
        this.exportDetailsLoading = false;
        this.exportService.saveToFileSystem(response);
      }, err => {
        this.exportDetailsLoading = false;
      });
  }

  ngOnDestroy(): void {
    if (this.dataSub) {
      this.dataSub.unsubscribe();
    }
  }


}
