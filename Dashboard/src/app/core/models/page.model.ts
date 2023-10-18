export class Page {
  //The number of elements in the page
  size: number = 0;
  //The total number of elements
  totalElements: number = 0;
  //The total number of pages
  totalPages: number = 0;
  //The current page number
  pageNumber: number = 0;


  constructor(metaData?) {
    this.totalElements = metaData && metaData.total ? metaData.total : 0;
    this.size = metaData && metaData.per_page ? metaData.per_page : 25;
    this.pageNumber = metaData && metaData.current_page ? metaData.current_page - 1 : 0;
    this.totalPages = metaData && metaData.last_page ? metaData.last_page - 1 : 0;
  }
}