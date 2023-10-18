import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ItemStatusesComponent } from './item-statuses.component';

describe('ItemStatusesComponent', () => {
  let component: ItemStatusesComponent;
  let fixture: ComponentFixture<ItemStatusesComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ItemStatusesComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ItemStatusesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
