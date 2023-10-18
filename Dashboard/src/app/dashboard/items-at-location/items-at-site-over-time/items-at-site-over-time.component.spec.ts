import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ItemsAtSiteOverTimeComponent } from './items-at-site-over-time.component';

describe('ItemsAtSiteOverTimeComponent', () => {
  let component: ItemsAtSiteOverTimeComponent;
  let fixture: ComponentFixture<ItemsAtSiteOverTimeComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ItemsAtSiteOverTimeComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ItemsAtSiteOverTimeComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
