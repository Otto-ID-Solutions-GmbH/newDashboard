import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ItemsAtSiteTableComponent } from './items-at-site-table.component';

describe('ItemsAtSiteTableComponent', () => {
  let component: ItemsAtSiteTableComponent;
  let fixture: ComponentFixture<ItemsAtSiteTableComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ItemsAtSiteTableComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ItemsAtSiteTableComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
